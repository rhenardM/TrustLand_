<?php

namespace App\Entity;

use App\Repository\PlotRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlotRepository::class)]
class Plot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $CadastreNumber = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $Area = null;

    #[ORM\Column(length: 255)]
    private ?string $Location = null;

    #[ORM\ManyToOne(inversedBy: 'plots')]
    private ?LandTitle $LandTitle = null;

    #[ORM\ManyToOne(inversedBy: 'plots')]
    private ?Surveryor $Surveryor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCadastreNumber(): ?string
    {
        return $this->CadastreNumber;
    }

    public function setCadastreNumber(string $CadastreNumber): static
    {
        $this->CadastreNumber = $CadastreNumber;

        return $this;
    }

    public function getArea(): ?string
    {
        return $this->Area;
    }

    public function setArea(string $Area): static
    {
        $this->Area = $Area;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->Location;
    }

    public function setLocation(string $Location): static
    {
        $this->Location = $Location;

        return $this;
    }

    public function getLandTitle(): ?LandTitle
    {
        return $this->LandTitle;
    }

    public function setLandTitle(?LandTitle $LandTitle): static
    {
        $this->LandTitle = $LandTitle;

        return $this;
    }

    public function getSurveryor(): ?Surveryor
    {
        return $this->Surveryor;
    }

    public function setSurveryor(?Surveryor $Surveryor): static
    {
        $this->Surveryor = $Surveryor;

        return $this;
    }
}
