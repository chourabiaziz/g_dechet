<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
  
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]

#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $prename = null;

    #[ORM\Column(length: 50)]
    private ?string $phone = null;

   


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registrationDate = null;

    #[ORM\Column (nullable: true)]
    private ?bool $block = null;

   

 
    #[ORM\Column(length: 255)]
    private ?string $countryCode = null;

    #[ORM\OneToMany(targetEntity: Processus::class, mappedBy: 'user')]
    private Collection $precessus;

    public function __construct()
    {
        $this->precessus = new ArrayCollection();
    }


 
 

    public function getFullName(): string
    {
        return trim($this->prename . ' ' . $this->name);
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has 
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrename(): ?string
    {
        return $this->prename;
    }

    public function setPrename(string $prename): static
    {
        $this->prename = $prename;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }
 
    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): static
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }
 
 
  

    public function getIsBlocked(): ?bool
    {
        return $this->block;
    }

    public function setIsBlocked(bool $block): static
    {
        $this->block = $block;

        return $this;
    }
  
  
  
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }
    
    public function setCountryCode(?string $countryCode): static
    {
        $this->countryCode = $countryCode;
    
        return $this;
    }

    /**
     * @return Collection<int, Processus>
     */
    public function getPrecessus(): Collection
    {
        return $this->precessus;
    }

    public function addPrecessu(Processus $precessu): static
    {
        if (!$this->precessus->contains($precessu)) {
            $this->precessus->add($precessu);
            $precessu->setUser($this);
        }

        return $this;
    }

    public function removePrecessu(Processus $precessu): static
    {
        if ($this->precessus->removeElement($precessu)) {
            // set the owning side to null (unless already changed)
            if ($precessu->getUser() === $this) {
                $precessu->setUser(null);
            }
        }

        return $this;
    }

  
}
