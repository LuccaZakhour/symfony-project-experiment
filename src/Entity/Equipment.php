<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
class Equipment
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups("read")]
    private $id;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(nullable: true, type: 'text')]
    #[Groups(['read', 'write'])]
    private $description;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    #[Groups(['read', 'write'])]
    private $serialNumber;

    #[ORM\Column(type:"boolean")]
    #[Groups(['read', 'write'])]
    private $isActive;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    #[Groups(['read', 'write'])]
    private $manufacturer;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    #[Groups(['read', 'write'])]
    private $status;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'equipment')]
    #[Groups(['read', 'write'])]
    private Collection $reservations;

    // Other properties, getters, and setters
    public function __construct()
    {
        $this->isActive = false;
        $this->reservations = new ArrayCollection();
    }
    
    // Add getter for reservations
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(?string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;
        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getManufacturer() {
		return $this->manufacturer;
	}
	
	/**
	 * @param mixed $manufacturer 
	 * @return self
	 */
	public function setManufacturer($manufacturer): self {
		$this->manufacturer = $manufacturer;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getStatus() {
		return $this->status;
	}
	
	/**
	 * @param mixed $status 
	 * @return self
	 */
	public function setStatus($status): self {
		$this->status = $status;
		return $this;
	}

    // toString
    public function __toString(): string
    {
        return $this->name;
    }
}
