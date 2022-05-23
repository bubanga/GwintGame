<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service\Game\Card;

use App\Entity\Card;
use App\Entity\Game;
use App\Entity\GameParticipant;
use App\Service\Game\Container\GameCard;
use App\Service\Game\Container\GameDeck;
use App\Service\Game\Container\PowerDeck;
use App\Service\Game\Exception\NotFoundCardException;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractCard implements CardInterface
{
    public const TYPE_CARD = [
        0  => NoSkill::class,
        1  => Freeze::class,
        2  => Fog::class,
        3  => Rain::class,
        4  => ClearSky::class,
        5  => PowerUp::class,
        6  => Medical::class,
        7  => Destruction::class,
        8  => Deserter::class,
        9  => Medical::class,
        10 => Commander::class,
        11 => Cooperative::class,
        12 => Brotherhood::class,
        13 => UnitDestruction::class,
    ];

    public const STATUS_CARD = [
        "devoted" => -2,
        "destroyed" => -1,
        "in_deck" => 0,
        "active" => 1,
        "is_use" => 2,
        "used" => 3,
    ];

    protected EntityManagerInterface $em;
    protected array $typePlayer;
    private Game $gameEntity;
    private GameParticipant $player;
    private bool $checkStatus = true;

    public function __construct(EntityManagerInterface $entityManager, Game $gameEntity, GameParticipant $player)
    {
        $this->em         = $entityManager;
        $this->gameEntity = $gameEntity;
        $typePlayer = [
            "player" => ($gameEntity->getAttacker()->getId() == $player->getId())? "Attacker" : "Victim",
            "enemy" => ($gameEntity->getAttacker()->getId() != $player->getId())? "Attacker" : "Victim"
        ];
        $this->typePlayer = $typePlayer;
    }

    /**
     * @throws NotFoundCardException
     */
    public function onCountPower(GameCard $card): int
    {
        $cardEntity = $this->getCardEntity($card->getId());
        $gameEntity = $this->getGameEntity();

        $power = $cardEntity->getPower();
        if ($cardEntity->getSpecial())
            return $power;

        if ($gameEntity->getFreeze() || $gameEntity->getFog() || $gameEntity->getRain())
            $power = 1;

        $player = $this->getPlayer();
        $power *= $player->getPowerUp();

        return $power;
    }

    public function onCountPowerOthers(GameCard $card, PowerDeck $powerDeck): PowerDeck
    {
        return $powerDeck;
    }

    /*public function onTurn()
    {
    }

    public function onRound()
    {
    }*/

    protected function getPlayer(): GameParticipant
    {
        return $this->player;
    }

    protected function setPlayer(GameParticipant $player)
    {
        $this->player = $player;
        $setPlayer = sprintf("set%s", $this->typePlayer['player']);
        $this->gameEntity->{$setPlayer}($player);
    }

    protected function getEnemy(): GameParticipant
    {
        $getEnemy = sprintf("get%s", $this->typePlayer['enemy']);
        return $this->gameEntity->{$getEnemy}();
    }

    protected function setEnemy(GameParticipant $enemy)
    {
        $setEnemy = sprintf("set%s", $this->typePlayer['enemy']);
        $this->gameEntity->{$setEnemy}($enemy);
    }

    protected function getPlayerDeck(): GameDeck
    {
        return GameDeck::generate($this->getPlayer()->getDeck());
    }

    protected function getEnemyDeck(): GameDeck
    {
        return GameDeck::generate($this->getEnemy()->getDeck());
    }

    protected function setPlayerDeck(GameDeck $gameDeck)
    {
        $player = $this->getPlayer();
        $player->setDeck($gameDeck->toArray());
        $this->setPlayer($player);
    }

    protected function setEnemyDeck(GameDeck $gameDeck)
    {
        $enemy = $this->getEnemy();
        $enemy->setDeck($gameDeck->toArray());
        $this->setPlayer($enemy);
    }

    /**
     * @throws NotFoundCardException
     */
    protected function activateCard(GameCard $card, int $statusAfter = self::STATUS_CARD['is_use'])
    {
        $gameDeck = $this->getPlayerDeck();
        $_card = $gameDeck->get($card->getKey());
        if (!$_card)
            throw new NotFoundCardException();

        if ($_card->getId() != $card->getId())
            throw new NotFoundCardException("Bad index"); //todo

        if ($this->checkStatus && $card->getStatus() != self::STATUS_CARD['active'])
            throw new NotFoundCardException("Bad status"); //todo

        $card->setStatus($statusAfter);
        $this->setPlayerDeck($gameDeck);
    }

    /**
     * @return bool
     */
    public function getCheckStatus(): bool
    {
        return $this->checkStatus;
    }

    /**
     * @param bool $checkStatus
     */
    public function setCheckStatus(bool $checkStatus): void
    {
        $this->checkStatus = $checkStatus;
    }

    /**
     * @throws NotFoundCardException
     */
    protected function getCardEntity(int $id): Card
    {
        $cardEntity = $this->em->getRepository(Card::class)->find($id);
        if (!$cardEntity instanceof Card)
            throw new NotFoundCardException();

        return $cardEntity;
    }

    /**
     * @return Game
     */
    protected function getGameEntity(): Game
    {
        return $this->gameEntity;
    }

    /**
     * @param Game $gameEntity
     */
    protected function setGameEntity(Game $gameEntity): void
    {
        $this->gameEntity = $gameEntity;
    }
}