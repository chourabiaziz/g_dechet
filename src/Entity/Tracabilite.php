<?php

namespace App\Entity;

use App\Repository\TracabiliteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TracabiliteRepository::class)]
class Tracabilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $historique = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateMiseAJour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHistorique(): ?string
    {
        return $this->historique;
    }

    public function setHistorique(string $historique): static
    {
        $this->historique = $historique;

        return $this;
    }

    public function getDateMiseAJour(): ?\DateTimeInterface
    {
        return $this->dateMiseAJour;
    }

    public function setDateMiseAJour(\DateTimeInterface $dateMiseAJour): static
    {
        $this->dateMiseAJour = $dateMiseAJour;

        return $this;
    }
    public function __toString(): string
    {
        return $this->historique;
    }
}
