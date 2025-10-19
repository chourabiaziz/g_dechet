<?php

namespace App\Entity;

use App\Repository\ProcessusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProcessusRepository::class)]
class Processus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'processuses')]
    private ?Dechet $dechet = null;

    #[ORM\ManyToOne(inversedBy: 'precessus')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'processuses')]
    private ?Autorite $autorite = null;

    #[ORM\ManyToMany(targetEntity: Dechet::class, mappedBy: 'processus')]
    private Collection $dechets;

   

    public function __construct()
    {
        $this->dechets = new ArrayCollection();
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
            $dechet->addProcessus($this);
        }

        return $this;
    }

    public function removeDechet(Dechet $dechet): static
    {
        if ($this->dechets->removeElement($dechet)) {
            $dechet->removeProcessus($this);
        }

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDechet(): ?Dechet
    {
        return $this->dechet;
    }

    public function setDechet(?Dechet $dechet): static
    {
        $this->dechet = $dechet;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAutorite(): ?Autorite
    {
        return $this->autorite;
    }

    public function setAutorite(?Autorite $autorite): static
    {
        $this->autorite = $autorite;

        return $this;
    }
    public function __toString(): string
    {
        return $this->nom;
    }
}
