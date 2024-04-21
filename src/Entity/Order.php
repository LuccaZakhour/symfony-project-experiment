<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
#[ORM\Table(name: "order_entity")]
class Order
{
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type:"integer")]
    #[Groups("read")]
    private $id;

    #[ORM\Column(type:"string", length:50)]
    #[Groups(['read', 'write'])]
    private $orderNumber;

    #[ORM\Column(type:"datetime")]
    #[Groups(['read', 'write'])]
    private $orderDate;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    #[Groups(['read', 'write'])]
    private $supplier;

    #[ORM\ManyToOne(targetEntity:"App\Entity\User", inversedBy:"orders")]
    #[ORM\JoinColumn(nullable:true)]
    private $orderedBy;

    #[ORM\Column(type:"string", length:50, nullable:true)]
    #[Groups(['read', 'write'])]
    private $status;

    // set totalAmount property here
    #[ORM\Column(type:"integer", nullable:true)]
    #[Groups(['read', 'write'])]
    private $totalAmount;

    // Other properties, getters, and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): self
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getSupplier(): ?string
    {
        return $this->supplier;
    }

    public function setSupplier(string $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getOrderedBy(): ?User
    {
        return $this->orderedBy;
    }

    public function setOrderedBy(?User $orderedBy): self
    {
        $this->orderedBy = $orderedBy;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getTotalAmount() {
		return $this->totalAmount;
	}
	
	/**
	 * @param mixed $totalAmount 
	 * @return self
	 */
	public function setTotalAmount($totalAmount): self {
		$this->totalAmount = $totalAmount;
		return $this;
	}
}
