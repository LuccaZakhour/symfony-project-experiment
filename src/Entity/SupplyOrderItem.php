<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
class SupplyOrderItem
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups("read")]
    private $id;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $itemName;

    #[ORM\Column(type:"integer")]
    #[Groups(['read', 'write'])]
    private $quantity;

    #[ORM\ManyToOne(targetEntity:"App\Entity\SupplyOrder", inversedBy:"items")]
    #[Groups(['read', 'write'])]
    private $supplyOrder;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    #[Groups(['read', 'write'])]
    private $price;

    // Other properties, getters, and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItemName(): ?string
    {
        return $this->itemName;
    }

    public function setItemName(string $itemName): self
    {
        $this->itemName = $itemName;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getSupplyOrder(): ?SupplyOrder
    {
        return $this->supplyOrder;
    }

    public function setSupplyOrder(?SupplyOrder $supplyOrder): self
    {
        $this->supplyOrder = $supplyOrder;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;
        return $this;
    }
}
