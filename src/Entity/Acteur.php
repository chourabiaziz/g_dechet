<?php

namespace App\Entity;

use App\Repository\ActeurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActeurRepository::class)]
class Acteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\OneToMany(targetEntity: Dechet::class, mappedBy: 'acteur')]
    private Collection $dechets;

    public function __construct()
    {
        $this->dechets = new ArrayCollection();
    }

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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Dechet>
     */
    public function getDechets(): Collection
    {
        return $this->dechets;
    }

    public function addDechet(Dechet $dechet): static
    {
        if (!$this->dechets->contains($dechet)) {
            $this->dechets->add($dechet);
            $dechet->setActeur($this);
        }

        return $this;
    }

    public function removeDechet(Dechet $dechet): static
    {
        if ($this->dechets->removeElement($dechet)) {
            // set the owning side to null (unless already changed)
            if ($dechet->getActeur() === $this) {
                $dechet->setActeur(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom;
    }
}
