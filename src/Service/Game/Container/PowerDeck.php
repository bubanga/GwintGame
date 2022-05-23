<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service\Game\Container;

class PowerDeck
{
    private array $cards;

    public function set(GameCard $gameCard, int $power)
    {
        $this->cards[$gameCard->getKey()] = $power;
    }

    public function get(GameCard $gameCard): int
    {
        return $this->cards[$gameCard->getKey()]?: 0;
    }
}