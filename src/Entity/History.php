<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'histories')]
    private ?User $userId = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $gameId = null;

    #[ORM\Column]
    private ?\DateTime $lastVisit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getGameId(): ?Game
    {
        return $this->gameId;
    }

    public function setGameId(Game $gameId): static
    {
        $this->gameId = $gameId;

        return $this;
    }

    public function getLastVisit(): ?\DateTime
    {
        return $this->lastVisit;
    }

    public function setLastVisit(\DateTime $lastVisit): static
    {
        $this->lastVisit = $lastVisit;

        return $this;
    }
}
