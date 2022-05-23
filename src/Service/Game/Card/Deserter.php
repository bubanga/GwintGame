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

class Deserter extends AbstractCard
{
    /**
     * @throws NotFoundCardException
     */
    public function onUse(SelectedCards $cards)
    {
        $card = $cards->end();
        $this->activateCard($card, AbstractCard::STATUS_CARD['devoted']);
        $enemyGameDeck = $this->getEnemyDeck();

        $card->setStatus(AbstractCard::STATUS_CARD['is_use']);
        $enemyGameDeck->add($card);
        $this->setEnemyDeck($enemyGameDeck);

        $playerGameDeck = $this->getPlayerDeck();
        $playerGameDeck_inDeck = $playerGameDeck->find()->allByField(AbstractCard::STATUS_CARD['in_deck'])->getFind();

        if (count($playerGameDeck_inDeck->getCards()) == 0) {
            return;
        } else if (count($playerGameDeck_inDeck->getCards()) == 1) {
            $randCard = array_rand($playerGameDeck_inDeck->getCards());
        } else {
            $randCard = array_rand($playerGameDeck_inDeck->getCards(), 2);
        }

        foreach ($randCard as $key) {
            $card = $playerGameDeck->get($key);
            $card->setStatus(AbstractCard::STATUS_CARD['active']);
            $playerGameDeck->set($key, $card);
        }

        $this->setPlayerDeck($playerGameDeck);
    }
}