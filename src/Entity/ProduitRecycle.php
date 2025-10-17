<?php

namespace App\Entity;

use App\Repository\ProduitRecycleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRecycleRepository::class)]
class ProduitRecycle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'produitRecycl')]
    private ?BoucleEconomieCirculaire $boucle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getBoucle(): ?BoucleEconomieCirculaire
    {
        return $this->boucle;
    }

    public function setBoucle(?BoucleEconomieCirculaire $boucle): static
    {
        $this->boucle = $boucle;

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom;
    }
}
