<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\StatusRepository;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
#[ApiResource]
class Status

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    /**
     * @var Collection<int, LandTitle>
     */
    #[ORM\OneToMany(targetEntity: LandTitle::class, mappedBy: 'Status')]
    private Collection $landTitles;

    public function __construct()
    {
        $this->landTitles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

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
            $landTitle->setStatus($this);
        }

        return $this;
    }

    public function removeLandTitle(LandTitle $landTitle): static
    {
        if ($this->landTitles->removeElement($landTitle)) {
            // set the owning side to null (unless already changed)
            if ($landTitle->getStatus() === $this) {
                $landTitle->setStatus(null);
            }
        }

        return $this;
    }
}
