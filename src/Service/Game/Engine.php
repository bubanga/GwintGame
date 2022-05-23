<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service\Game;


use App\Entity\Card;
use App\Entity\Game;
use App\Entity\Player;
use App\Entity\GameParticipant;
use App\Service\Game\Card\AbstractCard;
use App\Service\Game\Container\GameCard;
use App\Service\Game\Container\GameDeck;
use App\Service\Game\Container\PowerDeck;
use App\Service\Game\Container\SelectedCards;
use App\Service\Game\Exception\NotFoundCardException;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Game\Container\PlayerDeck;

class Engine
{
    public const FIELD_SWORD    = 1;
    public const FIELD_BOW      = 2;
    public const FIELD_CATAPULT = 3;

    public const DECK_UNIVERSAL     = 0;
    public const DECK_KINGDOM_NORTH = 1; //dodatkowa karta przy wygranej
    public const DECK_MONSTER       = 2; //jedna karta zostaje na planszy
    public const DECK_SKELLIGE      = 3; //wygrywa remis
    public const DECK_SCOIATAEL     = 4; //wybiera kto zaczyna

    public const STATUS_PREPARING = 0;
    public const STATUS_FIGHT     = 1;
    public const STATUS_FINISH    = 2;

    private EntityManagerInterface $em;
    private Game $gameEntity;
    private Player $player;

    public function __construct(EntityManagerInterface $entityManager, Game $gameEntity, Player $player)
    {
        $this->em         = $entityManager;
        $this->gameEntity = $gameEntity;
        $this->player     = $player;
    }

    public function tryUseCard(SelectedCards $cards): void
    {
        if ($cards->sizeof() == 0)
            return;

        $cardGame = $cards->end();
        $gameParticipantEntity = $this->getGameParticipant();
        $cardEntity = $this->em->getRepository(Card::class)->find($cardGame->getId());
        if (!$cardEntity instanceof Card)
            return;

        $cardBySkill    = AbstractCard::TYPE_CARD[$cardEntity->getSkill()];
        $objCardBySkill = new $cardBySkill($this->em, $this->gameEntity, $gameParticipantEntity);
        $objCardBySkill->onUse($cards);
        $this->checkDeckForPass(); //todo zalezy czy tu sie to zaktualizuje przez referencje
    }

    public function changeTurn(): void
    {
        $attacker = $this->gameEntity->getAttacker();
        $victim   = $this->gameEntity->getVictim();

        $current = $this->gameEntity->getTurn();
        $opponent = ($current->getId() == $attacker->getId())? $victim : $attacker;

        if (!$opponent->getPass()) {
            $this->gameEntity->setTurn($opponent);
        }

        $this->gameEntity->setTimeout(time() + 30);
    }

    /**
     * @throws NotFoundCardException
     */
    public function checkGame(): void
    {
        $attacker = $this->gameEntity->getAttacker();
        $victim   = $this->gameEntity->getVictim();

        $this->checkTimeout();

        if ($attacker->getPass() && $victim->getPass()) {
            $winner = $this->completeRound();
            if ($attacker->getWins() == 2 || $victim->getWins() == 2)
                $this->finishGame();
            else
                $this->createNextRound($winner);
        }
    }

    private function getGameParticipant(): GameParticipant
    {
        $gameParticipantEntity = $this->gameEntity->getAttacker();
        if ($gameParticipantEntity->getPlayer()->getId() != $this->player->getId())
            $gameParticipantEntity = $this->gameEntity->getVictim();

        return $gameParticipantEntity;
    }

    private function checkTimeout()
    {
        if ($this->gameEntity->getTimeout() >= time())
            return;

        if ($this->gameEntity->getStatus() == self::STATUS_PREPARING) {
            $this->gameEntity->setStatus(self::STATUS_FIGHT);
            return;
        }

        $turn = $this->gameEntity->getTurn();
        $turn->setPass(true);

        if ($turn->getId() == $this->gameEntity->getAttacker()->getId())
            $this->gameEntity->setAttacker($turn);
        else
            $this->gameEntity->setVictim($turn);

        $this->changeTurn();
    }

    private function checkDeckForPass()
    {
        $gameParticipantEntity = $this->getGameParticipant();
        $gameDeck = GameDeck::generate($gameParticipantEntity->getDeck());
        $find = $gameDeck
            ->find()
            ->allByStatus(AbstractCard::STATUS_CARD['active'])
            ->getFind()
        ;

        if (count($find->getCards()) == 0) {
            $gameParticipantEntity->setPass(true);
            if ($gameParticipantEntity->getId() == $this->gameEntity->getAttacker()->getId())
                $this->gameEntity->setAttacker($gameParticipantEntity);
            else
                $this->gameEntity->setVictim($gameParticipantEntity);
        }
    }

    /**
     * @throws NotFoundCardException
     */
    private function completeRound(): int
    {
        $attacker = $this->gameEntity->getAttacker();
        $victim = $this->gameEntity->getVictim();

        $powerAttacker  = self::countPowerField($this->em, $this->gameEntity, $attacker, self::FIELD_SWORD);
        $powerAttacker += self::countPowerField($this->em, $this->gameEntity, $attacker, self::FIELD_BOW);
        $powerAttacker += self::countPowerField($this->em, $this->gameEntity, $attacker, self::FIELD_CATAPULT);

        $powerVictim  = self::countPowerField($this->em, $this->gameEntity, $victim, self::FIELD_SWORD);
        $powerVictim += self::countPowerField($this->em, $this->gameEntity, $victim, self::FIELD_BOW);
        $powerVictim += self::countPowerField($this->em, $this->gameEntity, $victim, self::FIELD_CATAPULT);

        $weight = $powerAttacker <=> $powerVictim;
        if ($weight == 0) {
            if ($attacker->getFraction() == self::DECK_SKELLIGE && $victim->getFraction() == self::DECK_SKELLIGE)
                return 0;

            if ($attacker->getFraction() == self::DECK_SKELLIGE)
                return 1;

            if ($victim->getFraction() == self::DECK_SKELLIGE)
                return -1;
        }

        return $weight;
    }

    private function finishGame()
    {
        $this->gameEntity->setStatus(self::STATUS_FINISH);
        /*$attacker = $this->gameEntity->getAttacker();
        $victim = $this->gameEntity->getVictim();
        $winner = ($attacker->getWins() == 2)? $attacker : $victim;*/
    }

    private function createNextRound(int $lastWinner)
    {
        $attacker = $this->gameEntity->getAttacker();
        $victim = $this->gameEntity->getVictim();

        $attackerDeck = $this->getDeckForNextRound($attacker, ($lastWinner == 1));
        $attacker->setPass(false);
        $attacker->setPowerUp(1);
        $attacker->setDeck($attackerDeck->toArray());

        $victimDeck = $this->getDeckForNextRound($victim, ($lastWinner == -1));
        $victim->setPass(false);
        $victim->setPowerUp(1);
        $victim->setDeck($victimDeck->toArray());

        $this->gameEntity->setAttacker($attacker);
        $this->gameEntity->setVictim($victim);
        $this->gameEntity->setRound($this->gameEntity->getRound() + 1);
        $this->gameEntity->setFreeze(false);
        $this->gameEntity->setFog(false);
        $this->gameEntity->setRain(false);
        $this->gameEntity->setTimeout(time()+30);

        $turn = $lastWinner;
        if ($turn == 0)
            $turn = rand(0, 1);

        if ($turn == 1) {
            $this->gameEntity->setTurn($attacker);
        } elseif ($turn == -1 || $turn == 0) {
            $this->gameEntity->setTurn($victim);
        }
    }

    private function getDeckForNextRound(GameParticipant $participant, bool $winner): GameDeck
    {
        $gameDeck = GameDeck::generate($participant->getDeck());
        $randCard = function (GameDeck $gameDeck, int $status):?GameCard {
            $card = $gameDeck
                ->find()
                ->allByStatus($status)
                ->getFind()
                ->getRandCard();
            ;

            if (!$card)
                return null;

            return end($card);
        };

        $saveCard = null;
        if ($participant->getFraction() == self::DECK_MONSTER) {
            $saveCard = $randCard($gameDeck, AbstractCard::STATUS_CARD['is_use']);
            if ($saveCard)
                $saveCard->setStatus(AbstractCard::STATUS_CARD['is_use']);
        } else if ($participant->getFraction() == self::DECK_KINGDOM_NORTH && $winner) {
            $saveCard = $randCard($gameDeck, AbstractCard::STATUS_CARD['in_deck']);
            if ($saveCard)
                $saveCard->setStatus(AbstractCard::STATUS_CARD['active']);
        }

        foreach ($gameDeck->getCards() as $card) {
            if ($card->getStatus() == AbstractCard::STATUS_CARD['is_use'])
                $card->setStatus(AbstractCard::STATUS_CARD['used']);
        }

        if ($saveCard)
            $gameDeck->set($saveCard->getKey(), $saveCard);

        return $gameDeck;
    }

    /**
     * @throws NotFoundCardException
     */
    static public function countPowerField(EntityManagerInterface $em, Game $gameEntity, GameParticipant $gameParticipantEntity, int $field): int
    {
        $gameDeck = GameDeck::generate($gameParticipantEntity->getDeck());
        $find = $gameDeck
            ->find()
            ->allByStatus(AbstractCard::STATUS_CARD['active'])
            ->allByField($field)
            ->getFind()
        ;

        $powerDeck = new PowerDeck();
        $cardEntities = [];
        foreach ($find->getCards() as $gameCard) {
            $cardEntity = $em->getRepository(Card::class)->find($gameCard->getId());
            if (!$cardEntity instanceof Card)
                throw new NotFoundCardException(); //todo

            $cardBySkill    = AbstractCard::TYPE_CARD[$cardEntity->getSkill()];
            $objCardBySkill = new $cardBySkill($em, $gameEntity, $gameParticipantEntity);
            $power = $objCardBySkill->onCountPower($gameCard);
            $powerDeck->set($gameCard, $power);
            $cardEntities[$gameCard->getKey()] = $cardEntity;
        }

        foreach ($cardEntities as $key => $cardEntity) {
            $cardBySkill    = AbstractCard::TYPE_CARD[$cardEntity->getSkill()];
            $objCardBySkill = new $cardBySkill($em, $gameEntity, $gameParticipantEntity);
            $powerDeck      = $objCardBySkill->onCountPowerOthers($find->get($key), $powerDeck);
        }

        $power = 0;
        foreach ($find->getCards() as $gameCard) {
            $power += $powerDeck->get($gameCard);
        }

        return $power;
    }

    static public function createGame(EntityManagerInterface $entityManager, Player $attacker, Player $victim): Game
    {
        $randCard = function (GameDeck $gameDeck): GameDeck {
            $find = $gameDeck
                ->find()
                ->allByStatus(AbstractCard::STATUS_CARD['in_deck'])
                ->getFind();

            $cards = $find->getRandCard(10);
            foreach ($cards as $card) {
                $card->setStatus(AbstractCard::STATUS_CARD['active']);
                $gameDeck->set($card->getKey(), $card);
            }

            return $gameDeck;
        };

        $attackerDeck = PlayerDeck::generate($attacker->getDeck()->getCards());
        $attackerGameDeck = PlayerDeck::generateGameDeck($attackerDeck);
        $attackerGameDeck = $randCard($attackerGameDeck);
        $gameParticipantEntityAttacker = new GameParticipant();
        $gameParticipantEntityAttacker->setFraction($attackerDeck->getActiveDeck());
        $gameParticipantEntityAttacker->setDeck($attackerGameDeck->toArray());

        $victimDeck = PlayerDeck::generate($victim->getDeck()->getCards());
        $victimGameDeck = PlayerDeck::generateGameDeck($victimDeck);
        $victimGameDeck = $randCard($victimGameDeck);
        $gameParticipantEntityVictim = new GameParticipant();
        $gameParticipantEntityVictim->setFraction($victimDeck->getActiveDeck());
        $gameParticipantEntityVictim->setDeck($victimGameDeck->toArray());

        $rand = [
            $gameParticipantEntityAttacker,
            $gameParticipantEntityVictim
        ];

        $turnPlayer = array_rand($rand);

        $gameEntity = new Game();
        $gameEntity->setTurn($rand[$turnPlayer]);
        $gameEntity->setStatus(self::STATUS_PREPARING);
        $gameEntity->setTimeout(time() + 30); //todo czas na przygotwanie
        $gameEntity->setAttacker($gameParticipantEntityAttacker);
        $gameEntity->setVictim($gameParticipantEntityVictim);

        $entityManager->persist($gameEntity);
        return $gameEntity;
    }
}