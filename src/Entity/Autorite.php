<?php

namespace App\Entity;

use App\Repository\AutoriteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AutoriteRepository::class)]
class Autorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\OneToMany(targetEntity: Processus::class, mappedBy: 'autorite')]
    private Collection $processuses;

    public function __construct()
    {
        $this->processuses = new ArrayCollection();
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
            $processus->setAutorite($this);
        }

        return $this;
    }

    public function removeProcessus(Processus $processus): static
    {
        if ($this->processuses->removeElement($processus)) {
            // set the owning side to null (unless already changed)
            if ($processus->getAutorite() === $this) {
                $processus->setAutorite(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->nom;
    }
}
