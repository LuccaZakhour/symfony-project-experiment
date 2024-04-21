<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
class StorageType
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

    #[ORM\OneToMany(targetEntity:"App\Entity\Storage", mappedBy:"storageType")]
	#[Groups(['read', 'write'])]
    private $storages;

	#[ORM\Column(length: 255, type: 'string', nullable: true)]
	#[Groups(['read', 'write'])]
	private $shape;

    public function __construct()
    {
        $this->storages = new ArrayCollection();
    }

    // Other properties, getters, and setters

    // ... Getters and Setters
	public function getShape(): ?string
	{
		return $this->shape;
	}

	public function setShape(string $shape): self
	{
		$this->shape = $shape;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param mixed $id 
	 * @return self
	 */
	public function setId($id): self {
		$this->id = $id;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @param mixed $name 
	 * @return self
	 */
	public function setName($name): self {
		$this->name = $name;
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

	// toString
	public function __toString(): string
	{
		return $this->getName();
	}

	/**
	 * @return Collection|Storage[]
	 */
	public function getStorages(): Collection
	{
		return $this->storages;
	}

	public function addStorage(Storage $storage): self
	{
		if (!$this->storages->contains($storage)) {
			$this->storages[] = $storage;
			$storage->setStorageType($this);
		}

		return $this;
	}

	public function removeStorage(Storage $storage): self
	{
		if ($this->storages->removeElement($storage)) {
			// set the owning side to null (unless already changed)
			if ($storage->getStorageType() === $this) {
				$storage->setStorageType(null);
			}
		}

		return $this;
	}
}
