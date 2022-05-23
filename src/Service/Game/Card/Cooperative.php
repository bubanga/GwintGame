<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service\Game\Card;

use App\Service\Game\Container\GameCard;
use App\Service\Game\Container\GameDeck;
use App\Service\Game\Container\PowerDeck;
use App\Service\Game\Container\SelectedCards;
use App\Service\Game\Exception\NotFoundCardException;

class Cooperative extends AbstractCard
{

    /**
     * @throws NotFoundCardException
     */
    public function onUse(SelectedCards $cards)
    {
        $this->activateCard($cards->end());
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
        $gameDeck = GameDeck::generate($player->getDeck());
        $find = $gameDeck
            ->find()
            ->allByStatus(AbstractCard::STATUS_CARD['active'])
            ->allById($card->getId())
            ->getFind()
        ;

        $_power = $power;
        foreach ($find->getCards() as $gameCard) {
            if ($gameCard->getKey() != $card->getKey())
                $power += $_power;
        }

        $power *= $player->getPowerUp();
        return $power;
    }
}