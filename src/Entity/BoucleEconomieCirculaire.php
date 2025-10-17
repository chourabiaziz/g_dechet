<?php

namespace App\Entity;

use App\Repository\BoucleEconomieCirculaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoucleEconomieCirculaireRepository::class)]
class BoucleEconomieCirculaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $objectif = null;

    #[ORM\OneToMany(targetEntity: ProduitRecycle::class, mappedBy: 'boucle')]
    private Collection $produitRecycl;

    public function __construct()
    {
        $this->produitRecycl = new ArrayCollection();
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

    public function getObjectif(): ?string
    {
        return $this->objectif;
    }

    public function setObjectif(string $objectif): static
    {
        $this->objectif = $objectif;

        return $this;
    }

    /**
     * @return Collection<int, ProduitRecycle>
     */
    public function getProduitRecycl(): Collection
    {
        return $this->produitRecycl;
    }

    public function addProduitRecycl(ProduitRecycle $produitRecycl): static
    {
        if (!$this->produitRecycl->contains($produitRecycl)) {
            $this->produitRecycl->add($produitRecycl);
            $produitRecycl->setBoucle($this);
        }

        return $this;
    }

    public function removeProduitRecycl(ProduitRecycle $produitRecycl): static
    {
        if ($this->produitRecycl->removeElement($produitRecycl)) {
            // set the owning side to null (unless already changed)
            if ($produitRecycl->getBoucle() === $this) {
                $produitRecycl->setBoucle(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->nom;
    }
}
