<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
class SupplyOrder
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups("read")]
    private $id;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups("internal")]
    private $orderNumber;

    #[ORM\Column(type:"datetime")]
    #[Groups(['read', 'write'])]
    private $orderDate;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $status;

    #[ORM\ManyToOne(targetEntity:"App\Entity\User", inversedBy:"supplyOrders")]
    #[Groups(['read', 'write'])]
    private $orderedBy;

    #[ORM\OneToMany(targetEntity:"App\Entity\SupplyOrderItem", mappedBy:"supplyOrder")]
    #[Groups(['read', 'write'])]
    private $items;

    const STATUS_REQUESTED = 'Requested';
    const STATUS_PENDING_APPROVAL = 'Pending Approval';
    const STATUS_APPROVED = 'Approved';
    const STATUS_ORDERED = 'Ordered';
    const STATUS_SHIPPED = 'Shipped';
    const STATUS_PARTIALLY_RECEIVED = 'Partially Received';
    const STATUS_RECEIVED = 'Received';
    const STATUS_CHECKED = 'Checked';
    const STATUS_STORED = 'Stored';
    const STATUS_INVOICED = 'Invoiced';
    const STATUS_PAID = 'Paid';
    const STATUS_CLOSED = 'Closed';
    const STATUS_CANCELLED = 'Cancelled';
    const STATUS_RETURNED = 'Returned';
    const STATUS_ERROR = 'Error';
    const STATUS_ARCHIVED = 'Archived';

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
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

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function setItems(?array $items): self
    {
        $this->items = $items;
        return $this;
    }

    // toString
    public function __toString(): string
    {
        return $this->orderNumber;
    }
}
