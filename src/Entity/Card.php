<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CardRepository::class)
 */
class Card
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $unitType;

    /**
     * @ORM\Column(type="integer")
     */
    private $power;

    /**
     * @ORM\Column(type="integer")
     */
    private $skill;

    /**
     * @ORM\Column(type="boolean")
     */
    private $special;

    /**
     * @ORM\Column(type="integer")
     */
    private $fraction;

    public function __serialize(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'unity_type'  => $this->unitType,
            'power'       => $this->power,
            'skill'       => $this->skill,
            'special'     => $this->special,
            'fraction'    => $this->fraction,
        ];
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUnitType(): ?int
    {
        return $this->unitType;
    }

    public function setUnitType(int $unitType): self
    {
        $this->unitType = $unitType;

        return $this;
    }

    public function getPower(): ?int
    {
        return $this->power;
    }

    public function setPower(int $power): self
    {
        $this->power = $power;

        return $this;
    }

    public function getSkill(): ?int
    {
        return $this->skill;
    }

    public function setSkill(int $skill): self
    {
        $this->skill = $skill;

        return $this;
    }

    public function getSpecial(): ?bool
    {
        return $this->special;
    }

    public function setSpecial(bool $special): self
    {
        $this->special = $special;

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
}
