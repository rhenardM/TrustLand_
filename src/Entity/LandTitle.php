<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\LandTitleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LandTitleRepository::class)]
#[ApiResource]
class LandTitle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $pdfPath;

    #[ORM\Column(length: 255)]
    private ?string $hash = null;

    #[ORM\Column(length: 255)]
    private ?string $titleNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $issueDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $expirationDate = null;

    #[ORM\ManyToOne(inversedBy: 'landTitles')]
    private ?Owner $Owner = null;

    #[ORM\ManyToOne(inversedBy: 'landTitles')]
    private ?Status $Status = null;

    /**
     * @var Collection<int, Plot>
     */
    #[ORM\OneToMany(targetEntity: Plot::class, mappedBy: 'LandTitle')]
    private Collection $plots;

    // Ajouter les getters et setters
    public function getPdfPath(): ?string
    {
        return $this->pdfPath;
    }

    public function setPdfPath(string $pdfPath): self
    {
        $this->pdfPath = $pdfPath;
        return $this;
    }

    public function __construct()
    {
        $this->plots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function getTitleNumber(): ?string
    {
        return $this->titleNumber;
    }

    public function setTitleNumber(string $titleNumber): static
    {
        $this->titleNumber = $titleNumber;

        return $this;
    }

    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    public function setIssueDate(\DateTimeInterface $issueDate): static
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTimeInterface $expirationDate): static
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getOwner(): ?Owner
    {
        return $this->Owner;
    }

    public function setOwner(?Owner $Owner): static
    {
        $this->Owner = $Owner;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->Status;
    }

    public function setStatus(?Status $Status): static
    {
        $this->Status = $Status;

        return $this;
    }

    /**
     * @return Collection<int, Plot>
     */
    public function getPlots(): Collection
    {
        return $this->plots;
    }

    public function addPlot(Plot $plot): static
    {
        if (!$this->plots->contains($plot)) {
            $this->plots->add($plot);
            $plot->setLandTitle($this);
        }

        return $this;
    }

    public function removePlot(Plot $plot): static
    {
        if ($this->plots->removeElement($plot)) {
            // set the owning side to null (unless already changed)
            if ($plot->getLandTitle() === $this) {
                $plot->setLandTitle(null);
            }
        }

        return $this;
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
}
