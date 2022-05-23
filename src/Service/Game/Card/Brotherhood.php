<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service\Game\Card;


use App\Service\Game\Container\GameDeck;
use App\Service\Game\Container\SelectedCards;
use App\Service\Game\Exception\NotFoundCardException;

class Brotherhood extends AbstractCard
{
    /**
     * @throws NotFoundCardException
     */
    public function onUse(SelectedCards $cards)
    {
        $mainCardEntity = $this->getCardEntity($cards->end()->getId());
        $this->activateCard($cards->end());

        $gameDeck = GameDeck::generate($this->getPlayer()->getDeck());
        $gameDeck_inDeck = $gameDeck->find()->allByStatus(AbstractCard::STATUS_CARD['in_deck'])->getFind();
        $gameDeck_active = $gameDeck->find()->allByStatus(AbstractCard::STATUS_CARD['active'])->getFind();
        $gameDeckToSearch = array_merge($gameDeck_active->toArray(), $gameDeck_inDeck->toArray());
        $gameDeckToSearch = GameDeck::generate($gameDeckToSearch);

        foreach ($gameDeckToSearch->getCards() as $card) {
            $cardEntity = $this->getCardEntity($card->getId());

            if (AbstractCard::TYPE_CARD[$cardEntity->getSkill()] != Brotherhood::class ||
                $cardEntity->getUnitType() != $mainCardEntity->getUnitType()           ||
                $cardEntity->getSpecial() || $cardEntity->getPower() != $mainCardEntity->getPower()
            ) {
               continue;
            }

            $this->activateCard($card);
        }
    }
}