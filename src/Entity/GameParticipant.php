<?php

namespace App\Entity;

use App\Repository\GameParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameParticipantRepository::class)
 */
class GameParticipant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $pass = false;

    /**
     * @ORM\Column(type="integer")
     */
    private $wins = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $changeCard = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $powerUp = 1;

    /**
     * @ORM\Column(type="integer")
     */
    private $fraction;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="gameParticipants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\Column(type="json")
     */
    private $deck = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPass(): ?bool
    {
        return $this->pass;
    }

    public function setPass(bool $pass): self
    {
        $this->pass = $pass;

        return $this;
    }

    public function getWins(): ?int
    {
        return $this->wins;
    }

    public function setWins(int $wins): self
    {
        $this->wins = $wins;

        return $this;
    }

    public function getChangeCard(): ?int
    {
        return $this->changeCard;
    }

    public function setChangeCard(int $changeCard): self
    {
        $this->changeCard = $changeCard;

        return $this;
    }

    public function getPowerUp(): ?int
    {
        return $this->powerUp;
    }

    public function setPowerUp(int $powerUp): self
    {
        $this->powerUp = $powerUp;

        return $this;
    }

    public function getFraction(): ?int
    {
        return $this->fraction;
    }

    public function setFraction(int $fraction): self
    {
        $this->fraction = $fraction;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getDeck(): ?array
    {
        return $this->deck;
    }

    public function setDeck(array $deck): self
    {
        $this->deck = $deck;

        return $this;
    }
}
