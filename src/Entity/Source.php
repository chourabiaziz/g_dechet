<?php

namespace App\Entity;

use App\Repository\SourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SourceRepository::class)]
class Source
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\OneToMany(targetEntity: Dechet::class, mappedBy: 'source')]
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
            $dechet->setSource($this);
        }

        return $this;
    }

    public function removeDechet(Dechet $dechet): static
    {
        if ($this->dechets->removeElement($dechet)) {
            // set the owning side to null (unless already changed)
            if ($dechet->getSource() === $this) {
                $dechet->setSource(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->nom;
    }
}
