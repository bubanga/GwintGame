<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service\Game\Container;

use App\Service\Game\Card\AbstractCard;

class GameCard
{
    private ?int $key = null;
    private int $id;
    private int $status;
    private int $field;
    private array $info;

    /**
     * @return int
     */
    public function getKey(): ?int
    {
        return $this->key;
    }

    /**
     * @param int $key
     */
    public function setKey(int $key): void
    {
        $this->key = $key;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status = AbstractCard::STATUS_CARD['in_deck']): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getField(): int
    {
        return $this->field;
    }

    /**
     * @param int $field
     */
    public function setField(int $field = -1): void
    {
        $this->field = $field;
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * @param array $info
     */
    public function setInfo(array $info = []): void
    {
        $this->info = $info;
    }

    public function toArray():array
    {
        return [
            $this->getKey() => [
                $this->getId(),
                $this->getStatus(),
                $this->getField(),
                $this->getInfo()
            ]
        ];
    }

    public static function generate(array $card): self
    {
        $obj = new GameCard();
        $key = array_key_last($card);
        $obj->setKey($key);
        $obj->setId($card[$key][0]);
        $obj->setStatus($card[$key][1]);
        $obj->setField($card[$key][2]);
        $obj->setInfo($card[$key][3]);
        return $obj;
    }
}