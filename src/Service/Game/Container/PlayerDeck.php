<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service\Game\Container;

use App\Service\Game\Card\AbstractCard;
use App\Service\Game\Engine;

class PlayerDeck
{
    private int $activeDeck = 0;
    private array $cards = [
        Engine::DECK_KINGDOM_NORTH => [
            0 => null, //commander id card
            1 => [], //active id card
            2 => [], //not active id cards
        ],
        Engine::DECK_MONSTER => [
            0 => null, //commander id card
            1 => [], //active id card
            2 => [], //not active id cards
        ],
        Engine::DECK_SKELLIGE => [
            0 => null, //commander id card
            1 => [], //active id card
            2 => [], //not active id cards
        ],
        Engine::DECK_SCOIATAEL => [
            0 => null, //commander id card
            1 => [], //active id card
            2 => [], //not active id cards
        ],
    ];

    public function toArray(): array
    {
        return [
            $this->activeDeck,
            $this->cards
        ];
    }

    public function getActiveDeck(): int
    {
        return $this->activeDeck;
    }

    public function getCommander(int $fraction): ?int
    {
        if (!in_array($fraction, [Engine::DECK_KINGDOM_NORTH, Engine::DECK_MONSTER, Engine::DECK_SKELLIGE, Engine::DECK_SCOIATAEL]))
            throw new \InvalidArgumentException(""); //todo zla frakcja

        return $this->cards[$fraction][0];
    }

    public function getActiveCards(int $fraction): array
    {
        if (!in_array($fraction, [Engine::DECK_KINGDOM_NORTH, Engine::DECK_MONSTER, Engine::DECK_SKELLIGE, Engine::DECK_SCOIATAEL]))
            throw new \InvalidArgumentException(""); //todo zla frakcja

        return $this->cards[$fraction][1];
    }

    public static function generate(array $playerDeck): self
    {
        $obj = new PlayerDeck();
        $obj->activeDeck = $playerDeck[0];
        $obj->cards = $playerDeck[1];

        return $obj;
    }

    public static function generateGameDeck(PlayerDeck $playerDeck): GameDeck
    {
        $gameDeck = new GameDeck();
        $fraction = $playerDeck->getActiveDeck();
        $commander = $playerDeck->getCommander($fraction);
        $cards = $playerDeck->getActiveCards($fraction);
        foreach ($cards as $item) {
            $card = new GameCard();
            $card->setId($item);
            $card->setStatus();
            $card->setField();
            $card->setInfo();
            $gameDeck->add($card);
        }

        $card = new GameCard();
        $card->setId($commander);
        $card->setStatus(AbstractCard::STATUS_CARD['active']);
        $card->setField(9);
        $card->setInfo();
        $gameDeck->add($card);

        return $gameDeck;
    }
}