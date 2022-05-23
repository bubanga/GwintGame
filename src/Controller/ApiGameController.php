<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Controller;

use App\Entity\Card;
use App\Entity\Game;
use App\Entity\Player;
use App\Service\Game\Engine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiGameController extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/api/game/check_game", name="api_game_check_game")
     */
    public function checkGame(Request $request): JsonResponse
    {
        $idPlayer = $request->request->get('id_player');
        $playerEntity = $this->em->getRepository(Player::class)->find($idPlayer);
        if (!$playerEntity instanceof Player) {
            return new Response();//todo
        }

        $gameEntity = $this->em->getRepository(Game::class)->findActiveGameByPlayer($playerEntity);
        if (!$gameEntity) {
            return $this->json([
                "status" => "fail",
                "msg"    => "Not found active match"
            ]);
        }

        //todo odpalaj funkcje dla timeout czy cos takiego

        $turn = ($gameEntity->getTurn()->getId() == $playerEntity->getGameParticipants()->last()->getId())? "player" : "enemy";
        $dataPlayer = function (Player $player): array {
            $game = $player->getGame()->last();
            $arena = $game->getArena();
            $pass = ($game->getAttacker()->getId() == $player->getId())? $game->getAttackerPass() : $game->getVictimPass();
            $wins = ($game->getAttacker()->getId() == $player->getId())? $game->getAttackerWin() : $game->getVictimWin();
            $deck = ($game->getAttacker()->getId() == $player->getId())? $arena->getAttackerDeck() : $arena->getVictimDeck();
            $powerup = ($game->getAttacker()->getId() == $player->getId())? $arena->getAttackerPowerup() : $arena->getVictimPowerup();
            $fraction = ($game->getAttacker()->getId() == $player->getId())? $arena->getAttackerFraction() : $arena->getVictimFraction();
            $id = $player->getId();

            return [
                'id' => $id,
                'pass' => $pass,
                'wins' => $wins,
                'deck' => $deck,
                'powerup' => $powerup,
                'fraction' => $fraction
            ];
        };

        return $this->json([
            "status" => "ok",
            "msg"    => "Found active match",
            "data" => [
                "timeout" => $gameEntity->getTimeout(),
                "status"  => $gameEntity->getStatus(),
                "round"   => $gameEntity->getRound(),
                "turn"    => $turn,
                "player" => ($gameEntity->getAttacker()->getId() == $playerEntity->getId())? $dataPlayer[$gameEntity->getAttacker()] : $dataPlayer[$gameEntity->getVictim()],
                "enemy"  => ($gameEntity->getAttacker()->getId() != $playerEntity->getId())? $dataPlayer[$gameEntity->getAttacker()] : $dataPlayer[$gameEntity->getVictim()],
                "arena" => [
                    "freeze" => $gameEntity->getArena()->getFreeze(),
                    "fog" => $gameEntity->getArena()->getFog(),
                    "rain" => $gameEntity->getArena()->getRain(),
                ]
            ]
        ]);
    }

    /**
     * @Route("/api/game/check_status_search_game", name="api_game_check_status_search_game")
     */
    public function checkStatusSearchGame(Request $request): JsonResponse
    {
        $idPlayer = $request->request->get('id_player');
        $playerEntity = $this->em->getRepository(Player::class)->find($idPlayer);
        $searchGameEntity = $playerEntity->getSearchGames()->last();

        if (!$searchGameEntity || $searchGameEntity->getStatus() != 0) {
            return $this->json([
                "status" => "fail",
                "msg"    => "Player is not looking for a match"
            ]);
        }

        if ($searchGameEntity->getTimeout() < time()) {
            $searchGameEntity->setStatus(-1);

            if (!$searchGameEntity->getAttacker()->getIsBot())
                $searchGameEntity->getAttacker()->setSearchStatus(false);
            if (!$searchGameEntity->getVictim()->getIsBot())
                $searchGameEntity->getVictim()->setSearchStatus(false);

            $this->em->flush();
            return $this->json([
                "status" => "fail",
                "msg"    => "Timeout"
            ]);
        }

        if ($idPlayer == $searchGameEntity->getAttacker()->getId()) {
            $playerIsAccept = $searchGameEntity->getAttackerStatus();
            $enemyIsAccept  = $searchGameEntity->getVictimStatus();
        } else {
            $playerIsAccept = $searchGameEntity->getVictimStatus();
            $enemyIsAccept  = $searchGameEntity->getAttackerStatus();
        }

        return $this->json([
            "status" => "ok",
            "msg"    => "Waiting for the invitation to be accepted",
            "data"   => [
                'player'  => $playerIsAccept,
                'enemy'   => $enemyIsAccept,
                'timeout' => $searchGameEntity->getTimeout()
            ]
        ]);
    }

    /**
     * @Route("/api/game/accept_search_game", name="api_game_check_accept_search_game")
     */
    public function acceptSearchGame(Request $request): JsonResponse
    {
        $res = $this->checkStatusSearchGame($request);
        $content = json_decode($res->getContent(), true);
        if ($content['status'] == 'fail') {
            return $this->json([
                "status" => "fail",
                "msg" => $content['msg']
            ]);
        }

        if ($content['data']['player']) {
            return $this->json([
                "status" => "fail",
                "msg" => "The invitation is accepted"
            ]);
        }

        $idPlayer = $request->request->get('id_player');
        $playerEntity = $this->em->getRepository(Player::class)->find($idPlayer);
        $searchGameEntity = $playerEntity->getSearchGames()->last();

        if ($idPlayer == $searchGameEntity->getAttacker()->getId()) {
            $searchGameEntity->setAttackerStatus(1);
        } else {
            $searchGameEntity->setVictimStatus(1);
        }

        if ($searchGameEntity->getAttackerStatus() != $searchGameEntity->getVictimStatus()) { //1 == 1
            return $this->json([
                "status" => "ok",
                "msg" => "Invitation accepted"
            ]);
        }

        //Engine::createNewGame($this->em, $searchGameEntity->getAttacker(), $searchGameEntity->getVictim());
        $searchGameEntity->setStatus(1);
        if (!$searchGameEntity->getAttacker()->getIsBot())
            $searchGameEntity->getAttacker()->setSearchStatus(false);
        if (!$searchGameEntity->getVictim()->getIsBot())
            $searchGameEntity->getVictim()->setSearchStatus(false);
        $this->em->flush();

        return $this->json([
            "status" => "ok",
            "msg" => "Invitation accepted, start match"
        ]);
    }

    /**
     * @Route("/api/game/card/{id}", name="api_game_card_id")
     */
    public function card(int $id): JsonResponse
    {
        $cardEntity = $this->em->getRepository(Card::class)->find($id);
        if (!$cardEntity instanceof Card) {
            return $this->json([
                "status" => "fail",
                "msg" => "Not found card"
            ]);
        }

        return $this->json([
            "status" => "ok",
            "msg" => "Found card",
            "data" => serialize($cardEntity)
        ]);
    }

    public function changeFirstTurn(Request $request): JsonResponse
    {
        $idPlayer = $request->request->get('id_player');
        $turnPlayer = $request->request->get('turn_player');
        $playerEntity = $this->em->getRepository(Player::class)->find($idPlayer);
        $turnPlayerEntity = $this->em->getRepository(Player::class)->find($turnPlayer);

        $res = $this->checkStatusGame($request);
        $content = json_decode($res->getContent(), true);
        if ($content['status'] == 'fail') {
            return $this->json([
                'status' => 'fail',
                'msg' => $content['msg']
            ]);
        }

        /*if ($content['data']['status'] != 0 || $content['data']['player']['fraction'] != Engine::DECK_SCOIATAEL || $content['data']['enemy']['fraction'] == Engine::DECK_SCOIATAEL) {
            return $this->json([
                'status' => 'fail',
                'msg' => "You cannot change"
            ]);
        }*/


        if (!$turnPlayerEntity instanceof Player || ($turnPlayer != $content['data']['player']['id'] || $turnPlayer != $content['data']['enemy']['id'])) {
            return $this->json([
                'status' => 'fail',
                'msg' => "Not found player"
            ]);
        }

        $gameEntity = $playerEntity->getGame()->last();
        $gameEntity->setTurn($turnPlayerEntity);
        $this->em->flush();

        return $this->json([
            'status' => 'ok',
            'msg' => sprintf("Change turn for %s", (string) $turnPlayerEntity)
        ]);
    }

    public function changeCard(Request $request)
    {
        $idPlayer = $request->request->get('id_player');
        $playerEntity = $this->em->getRepository(Player::class)->find($idPlayer);
        if (!$playerEntity instanceof Player) {

        }
        $res = $this->checkStatusGame($request);
        $content = json_decode($res->getContent(), true);
        if ($content['status'] == 'fail') {
            return $this->json([
                'status' => 'fail',
                'msg' => $content['msg']
            ]);
        }

        $game = $playerEntity->getGame()->last();
        $changeCard = ($playerEntity->getId() == $game->getAttacker()->getId())? $game->getAttackerChangeCard() : $game->getVictimChangeCard();
        if ($content['data']['status'] != 0 || $changeCard > 2) {
            return $this->json([
                'status' => 'fail',
                'msg' => "You cannot change"
            ]);
        }

        //todo odpal zmienianie karty
        $this->em->flush();
        return $this->json([
            'status' => 'ok',
            'msg' => "You change card"
        ]);
    }

    public function playCard()
    {

    }
}