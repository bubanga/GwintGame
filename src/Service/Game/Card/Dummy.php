<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service\Game\Card;


use App\Service\Game\Container\SelectedCards;
use App\Service\Game\Exception\NotFoundCardException;

class Dummy extends AbstractCard
{

    /**
     * @throws NotFoundCardException
     */
    public function onUse(SelectedCards $cards)
    {
        $card = $cards->end();
        $this->activateCard($card);

        $cards->pop();
        if ($cards->sizeof() == 0)
            return;

        $card = $cards->end();
        $cardEntity = $this->getCardEntity($card->getId());
        if ($cardEntity->getSpecial() || $cardEntity->getUnitType() == 8 || $cardEntity->getUnitType() == 9)
            throw new NotFoundCardException(); //todo

        $card->setStatus(AbstractCard::STATUS_CARD['active']);
        $gameDeck = $this->getPlayerDeck();
        $gameDeck->set($card->getKey(), $card);
        $this->setPlayerDeck($gameDeck);
    }
}