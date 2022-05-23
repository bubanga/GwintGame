<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $round = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeout;

    /**
     * @ORM\OneToOne(targetEntity=GameParticipant::class, inversedBy="game", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $attacker;

    /**
     * @ORM\OneToOne(targetEntity=GameParticipant::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $victim;

    /**
     * @ORM\Column(type="boolean")
     */
    private $freeze = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $rain = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fog = false;

    /**
     * @ORM\OneToOne(targetEntity=GameParticipant::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $turn;

    public function __toString()
    {
        return $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(int $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getAttacker(): ?GameParticipant
    {
        return $this->attacker;
    }

    public function setAttacker(GameParticipant $attacker): self
    {
        $this->attacker = $attacker;

        return $this;
    }

    public function getVictim(): ?GameParticipant
    {
        return $this->victim;
    }

    public function setVictim(GameParticipant $victim): self
    {
        $this->victim = $victim;

        return $this;
    }

    public function getFreeze(): ?bool
    {
        return $this->freeze;
    }

    public function setFreeze(bool $freeze): self
    {
        $this->freeze = $freeze;

        return $this;
    }

    public function getRain(): ?bool
    {
        return $this->rain;
    }

    public function setRain(bool $rain): self
    {
        $this->rain = $rain;

        return $this;
    }

    public function getFog(): ?bool
    {
        return $this->fog;
    }

    public function setFog(bool $fog): self
    {
        $this->fog = $fog;

        return $this;
    }

    public function getTurn(): ?GameParticipant
    {
        return $this->turn;
    }

    public function setTurn(GameParticipant $turn): self
    {
        $this->turn = $turn;

        return $this;
    }
}
