<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
class Reservation
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups("read")]
    private $id;

    #[ORM\Column(type:"datetime")]
    #[Groups(['read', 'write'])]
    private $startTime;

    #[ORM\Column(type:"datetime")]
    #[Groups(['read', 'write'])]
    private $endTime;

    // set $reservationCode
    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $reservationCode;

    #[ORM\ManyToOne(targetEntity:"App\Entity\Equipment", inversedBy:"reservations")]
    #[Groups(['read', 'write'])]
    private $equipment;

    #[ORM\ManyToOne(targetEntity:"App\Entity\User", inversedBy:"reservations")]
    #[Groups(['read', 'write'])]
    private $user;

    #[ORM\Column(nullable: true, type: 'text')]
    #[Groups(['read', 'write'])]
    private $notes;

    // Other properties, getters, and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): self
    {
        $this->equipment = $equipment;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

	/**
	 * @param mixed $startTime 
	 * @return self
	 */
	public function setStartTime($startTime): self {
		$this->startTime = $startTime;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEndTime() {
		return $this->endTime;
	}
	
	/**
	 * @param mixed $endTime 
	 * @return self
	 */
	public function setEndTime($endTime): self {
		$this->endTime = $endTime;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getReservationCode() {
		return $this->reservationCode;
	}
	
	/**
	 * @param mixed $reservationCode 
	 * @return self
	 */
	public function setReservationCode($reservationCode): self {
		$this->reservationCode = $reservationCode;
		return $this;
	}
}
