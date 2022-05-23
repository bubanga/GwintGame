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

class Medical extends AbstractCard
{

    /**
     * @throws NotFoundCardException
     */
    public function onUse(SelectedCards $cards)
    {
        $card = $cards->end();
        $this->activateCard($card);
        $gameDeck = $this->getPlayerDeck();

        if ($cards->sizeof() == 0) {
            $find = $gameDeck->find()->allByStatus(AbstractCard::STATUS_CARD['used'])->getFind();
            foreach ($find->getCards() as $_card) {
                $_cardEntity = $this->getCardEntity($_card->getId());
                if (!$_cardEntity->getSpecial() || $_cardEntity->getUnitType() != 8 || $_cardEntity->getUnitType() != 9)
                    throw new NotFoundCardException(); //todo
            }
        }

        $cards->pop();
        $card = $cards->end();
        $cardEntity = $this->getCardEntity($card->getId());
        if ($cardEntity->getSpecial() || $cardEntity->getUnitType() == 8 || $cardEntity->getUnitType() == 9)
            throw new NotFoundCardException(); //todo

        $cardBySkill    = AbstractCard::TYPE_CARD[$cardEntity->getSkill()];
        $objCardBySkill = new $cardBySkill($this->em, $this->getGameEntity(), $this->getPlayer()); //todo czy to bedzie obserwowane
        $objCardBySkill->onUse($cards);
    }
}