<?php declare(strict_types=1);
/**
 * @author Jakub Gniecki
 * @copyright Jakub Gniecki <kubuspl@onet.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Service\Game\Container;


class GameDeck
{
    /**
     * @var GameCard[]
     */
    private array $cards;

    /**
     * @var GameCard[]
     */
    private array $findCards = [];

    /**
     * @param GameCard $card
     * @return void
     */
    public function add(GameCard $card)
    {
        $this->cards[] = $card;
        $key = array_key_last($this->cards[]);
        $card->setKey($key);
        $this->cards[$key] = $card;
    }

    /**
     * @param int $key
     * @return GameCard|null
     */
    public function get(int $key): ?GameCard
    {
        return (isset($this->cards[$key]))? $this->cards[$key] : null;
    }

    /**
     * @param int $key
     * @param GameCard $card
     * @return void
     */
    public function set(int $key, GameCard $card)
    {
        $card->setKey($key);
        $this->cards[$key] = $card;
    }

    /**
     * @return GameCard[]
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    public function __serialize(): array
    {
        $result = [];
        foreach ($this->cards as $card)
            $result = array_merge($result, $card->toArray());

        return $result;
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->cards as $card)
            $result = array_merge($result, $card->toArray());

        return $result;
    }

    public static function generate(array $cards): self
    {
        $obj = new GameDeck();
        foreach ($cards as $key => $card) {
            $obj->set($key, GameCard::generate([$key => $card]));
        }

        return $obj;
    }

    public function find(): self
    {
        $this->findCards = $this->cards;
        return $this;
    }

    public function allById(int $id, bool $negation = false): self
    {
        $this->findAll("getId", $id, $negation);
        return $this;
    }

    public function OneById(int $id, bool $negation = false): self
    {
        $this->findOne("getId", $id, $negation);
        return $this;
    }

    public function allByStatus(int $status, bool $negation = false): self
    {
        $this->findAll("getStatus", $status, $negation);
        return $this;
    }

    public function OneByStatus(int $status, bool $negation = false): self
    {
        $this->findOne("getStatus", $status, $negation);
        return $this;
    }

    public function allByField(int $field, bool $negation = false): self
    {
        $result = [];

        if ($field == 3) {
            foreach ($this->findCards as $card) {
                if (($negation && ($card->getField() != 1 || $card->getField() != 2)) || (!$negation && ($card->getField() == 1 || $card->getField() == 2)))
                    $result[$card->getKey()] = $card;
            }

            $this->findCards = $result;
        } else if ($field == 5) {
            foreach ($this->findCards as $card) {
                if (($negation && ($card->getField() != 1 || $card->getField() != 4)) || (!$negation && ($card->getField() == 1 || $card->getField() == 4)))
                    $result[$card->getKey()] = $card;
            }

            $this->findCards = $result;
        } else if ($field == 6) {
            foreach ($this->findCards as $card) {
                if (($negation && ($card->getField() != 2 || $card->getField() != 4)) || (!$negation && ($card->getField() == 2 || $card->getField() == 4)))
                    $result[$card->getKey()] = $card;
            }

            $this->findCards = $result;
        } else if ($field == 7) {
            foreach ($this->findCards as $card) {
                if (($negation && ($card->getField() != 1 || $card->getField() != 2 || $card->getField() != 4)) || (!$negation && (($card->getField() == 1 || $card->getField() == 2 || $card->getField() == 4))))
                    $result[$card->getKey()] = $card;
            }

            $this->findCards = $result;
        } else {
            $this->findAll("getField", $field, $negation);
        }

        return $this;
    }

    public function OneByField(int $field, bool $negation = false): self
    {
        $result = [];

        if ($field == 3) {
            foreach ($this->findCards as $card) {
                if (($negation && ($card->getField() != 1 || $card->getField() != 2)) || (!$negation && ($card->getField() == 1 || $card->getField() == 2))) {
                    $result[$card->getKey()] = $card;
                    break;
                }
            }

            $this->findCards = $result;
        } else if ($field == 5) {
            foreach ($this->findCards as $card) {
                if (($negation && ($card->getField() != 1 || $card->getField() != 4)) || (!$negation && ($card->getField() == 1 || $card->getField() == 4))) {
                    $result[$card->getKey()] = $card;
                    break;
                }
            }

            $this->findCards = $result;
        } else if ($field == 6) {
            foreach ($this->findCards as $card) {
                if (($negation && ($card->getField() != 2 || $card->getField() != 4)) || (!$negation && ($card->getField() == 2 || $card->getField() == 4))) {
                    $result[$card->getKey()] = $card;
                    break;
                }
            }

            $this->findCards = $result;
        } else if ($field == 7) {
            foreach ($this->findCards as $card) {
                if (($negation && ($card->getField() != 1 || $card->getField() != 2 || $card->getField() != 4)) || (!$negation && (($card->getField() == 1 || $card->getField() == 2 || $card->getField() == 4)))) {
                    $result[$card->getKey()] = $card;
                    break;
                }
            }

            $this->findCards = $result;
        } else {
            $this->findOne("getField", $field, $negation);
        }

        return $this;
    }

    private function findOne(string $method, $value, bool $negation)
    {
        $result = [];
        foreach ($this->findCards as $card) {
            if (($negation && $card->{$method}() != $value) || (!$negation && $card->{$method}() == $value)) {
                $result[$card->getKey()] = $card;
                break;
            }
        }

        $this->findCards = $result;
    }

    private function findAll(string $method, $value, bool $negation)
    {
        $result = [];
        foreach ($this->findCards as $card) {
            if (($negation && $card->{$method}() != $value) || (!$negation && $card->{$method}() == $value))
                $result[$card->getKey()] = $card;
        }

        $this->findCards = $result;
    }

    public function getFind(): GameDeck
    {
        return self::generate($this->findCards);
    }

    /**
     * @param int $count
     * @return GameCard[]|null
     */
    public function getRandCard(int $count = 1): ?array
    {
        if (count($this->cards) < $count) {
            return null;
        }

        $rand = array_rand($this->cards, $count);
        $result = [];
        foreach ($rand as $key) {
            $result[$key] = $this->get($key);
        }

        return $result;
    }
}