<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
class CatalogItem
{
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type:"integer")]
    #[Groups("read")]
    private $id;

    #[ORM\Column(type:"string", length:255)]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $sku;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    #[Groups(['read', 'write'])]
    private $price;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(['read', 'write'])]
    private $description;

    #[ORM\Column(type: "json", nullable: true)]
    #[Groups(['read', 'write'])]
    private $meta;

    #[ORM\ManyToOne(targetEntity: Supplier::class, inversedBy: 'catalogItems')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Supplier $supplier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
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

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function setMeta(?array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

	/**
	 * 
	 * @param mixed $id 
	 * @return self
	 */
	public function setId($id): self {
		$this->id = $id;
		return $this;
	}
}
