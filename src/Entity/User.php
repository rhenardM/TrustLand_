<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]

    /**
     * @ApiResource(
     *     collectionOperations={
     *         "get"={"security"="is_granted('ROLE_USER')"},
     *         "post"={"security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"}
     *     },
     *     itemOperations={
     *         "get"={"security"="is_granted('ROLE_USER')"}
     *     }
     * )
     * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
     */

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 50)]
    private ?string $lastName = null;

    #[ORM\Column(length: 15)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Owner>
     */
    #[ORM\OneToMany(targetEntity: Owner::class, mappedBy: 'user')]
    private Collection $owners;

    /**
     * @var Collection<int, Cadastre>
     */
    #[ORM\OneToMany(targetEntity: Cadastre::class, mappedBy: 'User')]
    private Collection $cadastres;

    /**
     * @var Collection<int, CTI>
     */
    #[ORM\OneToMany(targetEntity: CTI::class, mappedBy: 'User')]
    private Collection $cTIs;

    public function __construct()
    {
        $this->owners = new ArrayCollection();
        $this->cadastres = new ArrayCollection();
        $this->cTIs = new ArrayCollection();
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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array|string $roles): static
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Owner>
     */
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    public function addOwner(Owner $owner): static
    {
        if (!$this->owners->contains($owner)) {
            $this->owners->add($owner);
            $owner->setUser($this);
        }

        return $this;
    }

    public function removeOwner(Owner $owner): static
    {
        if ($this->owners->removeElement($owner)) {
            // set the owning side to null (unless already changed)
            if ($owner->getUser() === $this) {
                $owner->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cadastre>
     */
    public function getCadastres(): Collection
    {
        return $this->cadastres;
    }

    public function addCadastre(Cadastre $cadastre): static
    {
        if (!$this->cadastres->contains($cadastre)) {
            $this->cadastres->add($cadastre);
            $cadastre->setUser($this);
        }

        return $this;
    }

    public function removeCadastre(Cadastre $cadastre): static
    {
        if ($this->cadastres->removeElement($cadastre)) {
            // set the owning side to null (unless already changed)
            if ($cadastre->getUser() === $this) {
                $cadastre->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CTI>
     */
    public function getCTIs(): Collection
    {
        return $this->cTIs;
    }

    public function addCTI(CTI $cTI): static
    {
        if (!$this->cTIs->contains($cTI)) {
            $this->cTIs->add($cTI);
            $cTI->setUser($this);
        }

        return $this;
    }

    public function removeCTI(CTI $cTI): static
    {
        if ($this->cTIs->removeElement($cTI)) {
            // set the owning side to null (unless already changed)
            if ($cTI->getUser() === $this) {
                $cTI->setUser(null);
            }
        }

        return $this;
    }
}
