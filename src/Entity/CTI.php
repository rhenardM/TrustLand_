<?php

namespace App\Entity;

use App\Repository\CTIRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CTIRepository::class)]
class CTI
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $NameOffice = null;
    
    #[ORM\ManyToOne(inversedBy: 'cTIs')]
    private ?User $User = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameOffice(): ?string
    {
        return $this->NameOffice;
    }

    public function setNameOffice(string $NameOffice): static
    {
        $this->NameOffice = $NameOffice;

        return $this;
    }
    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }
}
