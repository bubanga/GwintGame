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

class Rain extends AbstractCard
{

    /**
     * @throws NotFoundCardException
     */
    public function onUse(SelectedCards $cards)
    {
        $this->activateCard($cards->end());
        $gameEntity = $this->getGameEntity();
        $gameEntity->setRain(true);
        $this->setGameEntity($gameEntity);
    }
}