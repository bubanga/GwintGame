<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service\Game\Card;

use App\Service\Game\Container\GameCard;
use App\Service\Game\Container\PowerDeck;
use App\Service\Game\Container\SelectedCards;

interface CardInterface
{
    public function onUse(SelectedCards $cards);
    public function onCountPower(GameCard $card): int;
    public function onCountPowerOthers(GameCard $card, PowerDeck $powerDeck): PowerDeck;

    /*public function onTurn();
    public function onRound();*/


}