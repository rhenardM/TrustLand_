<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OwnerRepository;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OwnerRepository::class)]
#[ApiResource]
class Owner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $firstName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'owners')]
    #[ORM\JoinColumn(nullable: true)]// modify this to true, modify integrity constraint for true
    #[Groups(['owner:write', 'owner:read'])]
    private ?User $user = null;

    /**
     * @var Collection<int, LandTitle>
     */
    #[ORM\OneToMany(targetEntity: LandTitle::class, mappedBy: 'Owner')]
    private Collection $landTitles;

    public function __construct()
    {
        $this->landTitles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeInterface $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

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
    /**
     * @return Collection<int, LandTitle>
     */
    public function getLandTitles(): Collection
    {
        return $this->landTitles;
    }
    public function addLandTitle(LandTitle $landTitle): static
    {
        if (!$this->landTitles->contains($landTitle)) {
            $this->landTitles->add($landTitle);
            $landTitle->setOwner($this);
        }
        return $this;
    }
    public function removeLandTitle(LandTitle $landTitle): static
    {
        if ($this->landTitles->removeElement($landTitle)) {
            // set the owning side to null (unless already changed)
            if ($landTitle->getOwner() === $this) {
                $landTitle->setOwner(null);
            }
        }
        return $this;
    }
}
