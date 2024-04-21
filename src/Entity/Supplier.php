<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
class Supplier
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups("read")]
    private $id;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $description;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $contactEmail;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $contactPhone;

    #[ORM\OneToMany(targetEntity:"App\Entity\CatalogItem", mappedBy:"supplier")]
    #[Groups(['read', 'write'])]
    private $catalogItems;

    public function __construct()
    {
        $this->catalogItems = new ArrayCollection();
    }

    // Other properties, getters, and setters

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

    /**
     * @return Collection|CatalogItem[]
     */
    public function getCatalogItems(): Collection
    {
        return $this->catalogItems;
    }

    public function addCatalogItem(CatalogItem $catalogItem): self
    {
        if (!$this->catalogItems->contains($catalogItem)) {
            $this->catalogItems[] = $catalogItem;
            //$catalogItem->setSupplier($this);
        }

        return $this;
    }

    public function removeCatalogItem(CatalogItem $catalogItem): self
    {
        if ($this->catalogItems->contains($catalogItem)) {
            $this->catalogItems->removeElement($catalogItem);
            // set the owning side to null (unless already changed)
            /*
            if ($catalogItem->getSupplier() === $this) {
                $catalogItem->setSupplier(null);
            }
            */
        }

        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * @param mixed $description 
	 * @return self
	 */
	public function setDescription($description): self {
		$this->description = $description;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getContactEmail() {
		return $this->contactEmail;
	}
	
	/**
	 * @param mixed $contactEmail 
	 * @return self
	 */
	public function setContactEmail($contactEmail): self {
		$this->contactEmail = $contactEmail;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getContactPhone() {
		return $this->contactPhone;
	}
	
	/**
	 * @param mixed $contactPhone 
	 * @return self
	 */
	public function setContactPhone($contactPhone): self {
		$this->contactPhone = $contactPhone;
		return $this;
	}
}
