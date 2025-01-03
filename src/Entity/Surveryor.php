<?php  

namespace App\Entity;  

use App\Repository\SurveryorRepository;  
use Doctrine\Common\Collections\ArrayCollection;  
use Doctrine\Common\Collections\Collection;  
use Doctrine\ORM\Mapping as ORM;  

#[ORM\Entity(repositoryClass: SurveryorRepository::class)]  
class Surveryor  
{  
    #[ORM\Id]  
    #[ORM\GeneratedValue]  
    #[ORM\Column]  
    private ?int $id = null;  

    #[ORM\Column(length: 25)]  
    private ?string $name = null; // Changement de "Name" à "name"  

    #[ORM\Column(length: 25)]  
    private ?string $firstName = null; // Changement de "FirstName" à "firstName"  

    #[ORM\Column(length: 15)]  
    private ?string $phone = null;  

    #[ORM\Column(length: 50)]  
    private ?string $email = null; // Changement de "Email" à "email"  

    /**  
     * @var Collection<int, Plot>  
     */  
    #[ORM\OneToMany(targetEntity: Plot::class, mappedBy: 'surveryor')] // Changement de "Surveryor" à "surveryor"  
    private Collection $plots;  

    public function __construct()  
    {  
        $this->plots = new ArrayCollection();  
    }  

    public function getId(): ?int  
    {  
        return $this->id;  
    }  

    public function getName(): ?string  
    {  
        return $this->name; // Correspondance avec la propriété  
    }  

    public function setName(string $name): static  
    {  
        $this->name = $name; // Correspondance avec la propriété  

        return $this;  
    }  

    public function getFirstName(): ?string  
    {  
        return $this->firstName; // Correspondance avec la propriété  
    }  

    public function setFirstName(string $firstName): static  
    {  
        $this->firstName = $firstName; // Correspondance avec la propriété  

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

    public function getEmail(): ?string  
    {  
        return $this->email; // Correspondance avec la propriété  
    }  

    public function setEmail(string $email): static  
    {  
        $this->email = $email; // Correspondance avec la propriété  

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
            $plot->setSurveryor($this);  
        }  

        return $this;  
    }  

    public function removePlot(Plot $plot): static  
    {  
        if ($this->plots->removeElement($plot)) {  
            // set the owning side to null (unless already changed)  
            if ($plot->getSurveryor() === $this) {  
                $plot->setSurveryor(null);  
            }  
        }  

        return $this;  
    }  
}