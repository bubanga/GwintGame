<?php

namespace App\Entity;

use App\Repository\SearchGameRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SearchGameRepository::class)
 */
class SearchGame
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="searchGames")
     * @ORM\JoinColumn(nullable=false)
     */
    private $attacker;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $victim;

    /**
     * @ORM\Column(type="integer")
     */
    private $attackerStatus;

    /**
     * @ORM\Column(type="integer")
     */
    private $victimStatus;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeout;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttacker(): ?Player
    {
        return $this->attacker;
    }

    public function setAttacker(?Player $attacker): self
    {
        $this->attacker = $attacker;

        return $this;
    }

    public function getVictim(): ?Player
    {
        return $this->victim;
    }

    public function setVictim(?Player $victim): self
    {
        $this->victim = $victim;

        return $this;
    }

    public function getAttackerStatus(): ?int
    {
        return $this->attackerStatus;
    }

    public function setAttackerStatus(int $attackerStatus): self
    {
        $this->attackerStatus = $attackerStatus;

        return $this;
    }

    public function getVictimStatus(): ?int
    {
        return $this->victimStatus;
    }

    public function setVictimStatus(int $victimStatus): self
    {
        $this->victimStatus = $victimStatus;

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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
