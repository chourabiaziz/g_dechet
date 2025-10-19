<?php

namespace App\Entity;

use App\Repository\DechetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DechetRepository::class)]
class Dechet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?float $quantite = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateProduction = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\ManyToOne(inversedBy: 'dechets')]
    private ?Source $source = null;

    #[ORM\ManyToOne(inversedBy: 'dechets')]
    private ?Infrastructure $infrastructure = null;

    #[ORM\ManyToOne(inversedBy: 'dechets')]
    private ?Acteur $acteur = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ProduitRecycle $produitrecycle = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Tracabilite $tracabilite = null;

    #[ORM\OneToMany(targetEntity: Processus::class, mappedBy: 'dechet')]
    private Collection $processuses;

    public function __construct()
    {
        $this->dateProduction = new \DateTime();
        $this->processuses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDateProduction(): ?\DateTimeInterface
    {
        return $this->dateProduction;
    }

    public function setDateProduction(\DateTimeInterface $dateProduction): static
    {
        $this->dateProduction = $dateProduction;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function setSource(?Source $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getInfrastructure(): ?Infrastructure
    {
        return $this->infrastructure;
    }

    public function setInfrastructure(?Infrastructure $infrastructure): static
    {
        $this->infrastructure = $infrastructure;

        return $this;
    }

    public function getActeur(): ?Acteur
    {
        return $this->acteur;
    }

    public function setActeur(?Acteur $acteur): static
    {
        $this->acteur = $acteur;

        return $this;
    }

    public function getProduitrecycle(): ?ProduitRecycle
    {
        return $this->produitrecycle;
    }

    public function setProduitrecycle(?ProduitRecycle $produitrecycle): static
    {
        $this->produitrecycle = $produitrecycle;

        return $this;
    }

    public function getTracabilite(): ?Tracabilite
    {
        return $this->tracabilite;
    }

    public function setTracabilite(?Tracabilite $tracabilite): static
    {
        $this->tracabilite = $tracabilite;

        return $this;
    }

    /**
     * @return Collection<int, Processus>
     */
    public function getProcessuses(): Collection
    {
        return $this->processuses;
    }

    public function addProcessus(Processus $processus): static
    {
        if (!$this->processuses->contains($processus)) {
            $this->processuses->add($processus);
            $processus->setDechet($this);
        }

        return $this;
    }

    public function removeProcessus(Processus $processus): static
    {
        if ($this->processuses->removeElement($processus)) {
            // set the owning side to null (unless already changed)
            if ($processus->getDechet() === $this) {
                $processus->setDechet(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->type;
    }
}
