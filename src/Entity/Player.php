<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 */
class Player
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
    private $username;

    /**
     * @ORM\OneToOne(targetEntity=Deck::class, mappedBy="player", cascade={"persist", "remove"})
     */
    private $deck;

    /**
     * @ORM\OneToMany(targetEntity=SearchGame::class, mappedBy="attacker")
     */
    private $searchGames;

    /**
     * @ORM\Column(type="boolean")
     */
    private $searchStatus;

    /**
     * @ORM\Column(type="integer")
     */
    private $lvl;

    /**
     * @ORM\Column(type="integer")
     */
    private $exp;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBot;

    /**
     * @ORM\OneToMany(targetEntity=GameParticipant::class, mappedBy="player")
     */
    private $gameParticipants;

    public function __toString()
    {
        return $this->username;
    }

    public function __construct()
    {
        $this->searchGames = new ArrayCollection();
        $this->gameParticipants = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getDeck(): ?Deck
    {
        return $this->deck;
    }

    public function setDeck(Deck $deck): self
    {
        // set the owning side of the relation if necessary
        if ($deck->getPlayer() !== $this) {
            $deck->setPlayer($this);
        }

        $this->deck = $deck;

        return $this;
    }

    /**
     * @return Collection<int, SearchGame>
     */
    public function getSearchGames(): Collection
    {
        return $this->searchGames;
    }

    public function addSearchGame(SearchGame $searchGame): self
    {
        if (!$this->searchGames->contains($searchGame)) {
            $this->searchGames[] = $searchGame;
            $searchGame->setAttacker($this);
        }

        return $this;
    }

    public function removeSearchGame(SearchGame $searchGame): self
    {
        if ($this->searchGames->removeElement($searchGame)) {
            // set the owning side to null (unless already changed)
            if ($searchGame->getAttacker() === $this) {
                $searchGame->setAttacker(null);
            }
        }

        return $this;
    }

    public function getSearchStatus(): ?bool
    {
        return $this->searchStatus;
    }

    public function setSearchStatus(bool $searchStatus): self
    {
        $this->searchStatus = $searchStatus;

        return $this;
    }

    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    public function setLvl(int $lvl): self
    {
        $this->lvl = $lvl;

        return $this;
    }

    public function getExp(): ?int
    {
        return $this->exp;
    }

    public function setExp(int $exp): self
    {
        $this->exp = $exp;

        return $this;
    }

    public function getIsBot(): ?bool
    {
        return $this->isBot;
    }

    public function setIsBot(bool $isBot): self
    {
        $this->isBot = $isBot;

        return $this;
    }

    /**
     * @return Collection<int, GameParticipant>
     */
    public function getGameParticipants(): Collection
    {
        return $this->gameParticipants;
    }

    public function addGameParticipant(GameParticipant $gameParticipant): self
    {
        if (!$this->gameParticipants->contains($gameParticipant)) {
            $this->gameParticipants[] = $gameParticipant;
            $gameParticipant->setPlayer($this);
        }

        return $this;
    }

    public function removeGameParticipant(GameParticipant $gameParticipant): self
    {
        if ($this->gameParticipants->removeElement($gameParticipant)) {
            // set the owning side to null (unless already changed)
            if ($gameParticipant->getPlayer() === $this) {
                $gameParticipant->setPlayer(null);
            }
        }

        return $this;
    }
}
