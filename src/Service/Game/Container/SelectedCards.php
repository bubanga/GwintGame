<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service\Game\Container;


use App\Service\Game\Card\AbstractCard;

class SelectedCards
{
    private array $cards = [];

    public function set(int $key, int $idCard, int $field): void
    {
        $obj = new GameCard();
        $obj->setKey($key);
        $obj->setId($idCard);
        $obj->setStatus(AbstractCard::STATUS_CARD['active']);
        $obj->setField($field);
        $obj->setInfo();
        $this->cards[$key] = $obj;
    }

    public function remove(int $key): void
    {
        unset($this->cards[$key]);
    }

    public function pop()
    {
        if ($this->sizeof() > 0)
            array_pop($this->cards);
    }

    public function end(): ?GameCard
    {
        $r = end($this->cards);
        return ($r)?: null;
    }

    public function lastKey(): ?int
    {
        return array_key_last($this->cards);
    }

    public function sizeof(): int
    {
        return count($this->cards);
    }
}