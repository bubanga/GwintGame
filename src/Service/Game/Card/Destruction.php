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

class Destruction extends AbstractCard
{
    use DestructionTrait;

    /**
     * @throws NotFoundCardException
     */
    public function onUse(SelectedCards $cards)
    {
        $this->activateCard($cards->end(), AbstractCard::STATUS_CARD['destroyed']);

        $playerGameDeck = $this->getPlayerDeck();
        $playerCardsToDestroy = $this->findCardToDestroy($playerGameDeck);

        $enemyGameDeck = $this->getEnemyDeck();
        $enemyCardsToDestroy = $this->findCardToDestroy($enemyGameDeck);


        if ($playerCardsToDestroy == [] && $enemyCardsToDestroy == [])
            return;

        krsort($playerCardsToDestroy);
        krsort($enemyCardsToDestroy);
        $keyPlayer = key($playerCardsToDestroy);
        $keyEnemy = key($enemyCardsToDestroy);
        $weight = $keyPlayer <=> $keyEnemy;

        if ($weight == 0) {
            $playerGameDeck = $this->setCardsToDestroyed($playerGameDeck, $playerCardsToDestroy[$keyPlayer]);
            $enemyGameDeck  = $this->setCardsToDestroyed($enemyGameDeck,  $enemyCardsToDestroy[$keyEnemy]);
            $this->setPlayerDeck($playerGameDeck);
            $this->setEnemyDeck($enemyGameDeck);
        } else if ($weight == 1) {
            $playerGameDeck = $this->setCardsToDestroyed($playerGameDeck, $playerCardsToDestroy[$keyPlayer]);
            $this->setPlayerDeck($playerGameDeck);
        } else {
            $enemyGameDeck  = $this->setCardsToDestroyed($enemyGameDeck,  $enemyCardsToDestroy[$keyEnemy]);
            $this->setEnemyDeck($enemyGameDeck);
        }
    }
}