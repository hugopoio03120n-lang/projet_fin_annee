<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SerieRepository::class)]
class Serie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $game = null;

    /**
     * @var Collection<int, Game>
     */
    #[ORM\ManyToMany(targetEntity: Game::class, inversedBy: 'series')]
    private Collection $gameSeries;

    public function __construct()
    {
        $this->gameSeries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getGame(): ?string
    {
        return $this->game;
    }

    public function setGame(?string $game): static
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGameSeries(): Collection
    {
        return $this->gameSeries;
    }

    public function addGameSeries(Game $gameSeries): static
    {
        if (!$this->gameSeries->contains($gameSeries)) {
            $this->gameSeries->add($gameSeries);
        }

        return $this;
    }

    public function removeGameSeries(Game $gameSeries): static
    {
        $this->gameSeries->removeElement($gameSeries);

        return $this;
    }
}
