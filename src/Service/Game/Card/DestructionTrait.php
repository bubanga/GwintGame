<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service\Game\Card;

use App\Service\Game\Container\GameDeck;
use App\Service\Game\Exception\NotFoundCardException;

trait DestructionTrait
{
    /**
     * @throws NotFoundCardException
     */
    private function findCardToDestroy(GameDeck $gameDeck):array
    {
        $gameDeck_isUse = $gameDeck->find()->allByStatus(AbstractCard::STATUS_CARD['is_use'])->getFind();
        $cardsToDestroyed = [];

        foreach ($gameDeck_isUse->getCards() as $card) {
            $cardEntity = $this->getCardEntity($card->getId());
            if ($cardEntity->getSpecial() || $cardEntity->getUnitType() == 8 || $cardEntity->getUnitType() == 9)
                continue;

            $cardsToDestroyed[$cardEntity->getPower()][] = $card->getKey();
        }

        return $cardsToDestroyed;
    }

    private function setCardsToDestroyed(GameDeck $gameDeck, array $keyList): GameDeck
    {
        foreach ($keyList as $key) {
            $card = $gameDeck->get($key);
            $card->setStatus(AbstractCard::STATUS_CARD['destroyed']);
            $gameDeck->set($key, $card);
        }

        return $gameDeck;
    }
}