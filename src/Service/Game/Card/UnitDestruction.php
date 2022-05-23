<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service\Game\Card;

use App\Service\Game\Container\SelectedCards;
use App\Service\Game\Engine;
use App\Service\Game\Exception\NotFoundCardException;

class UnitDestruction extends AbstractCard
{
    use DestructionTrait;

    /**
     * @throws NotFoundCardException
     */
    public function onUse(SelectedCards $cards)
    {
        $card = $cards->end();

        $this->activateCard($cards->end());
        $power = Engine::countPowerField($this->em, $this->getGameEntity(), $this->getEnemy(), $card->getField());
        if ($power < 10)
            return;

        $enemyGameDeck = $this->getEnemyDeck();
        $find = $enemyGameDeck
            ->find()
            ->allByField($card->getField())
            ->getFind()
        ;
        $enemyCardsToDestroy = $this->findCardToDestroy($find);
        if ($enemyCardsToDestroy == [])
            return;

        krsort($enemyCardsToDestroy);
        $key = key($enemyCardsToDestroy);

        $enemyDeck = $this->setCardsToDestroyed($enemyGameDeck,  $enemyCardsToDestroy[$key]);
        $this->setEnemyDeck($enemyDeck);
    }
}