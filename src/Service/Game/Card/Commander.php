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

class Commander extends AbstractCard
{
    /**
     * @throws NotFoundCardException
     */
    public function onUse(SelectedCards $cards)
    {
        $this->activateCard($cards->end());
    }

    public function onCountPowerOthers(GameCard $card, PowerDeck $powerDeck): PowerDeck
    {
        $player = $this->getPlayer();
        $gameDeck = GameDeck::generate($player->getDeck());
        $find = $gameDeck
            ->find()
            ->allByStatus(AbstractCard::STATUS_CARD['active'])
            ->allByField($card->getField())
            ->getFind()
        ;

        foreach ($find->getCards() as $gameCard) {
            if ($gameCard->getKey() != $card->getKey())
                $powerDeck->set($gameCard, $powerDeck->get($gameCard) + 1);
        }

        return $powerDeck;
    }
}