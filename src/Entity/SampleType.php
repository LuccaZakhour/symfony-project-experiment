<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class SampleType
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
	#[Groups("read")]
    private $id;

	#[ORM\Column(type: "integer", nullable: true)]
	#[Groups("internal")]
	private $sampleTypeID;

    #[ORM\Column(length: 255, type: 'string')]
	#[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(type:"string", length:255, nullable:true)]
	#[Groups(['read', 'write'])]
    private $description;

    #[ORM\Column(type:"datetime")]
	#[Groups(['read', 'write'])]
    private $createdAt;

    #[ORM\Column(type:"datetime")]
	#[Groups(['read', 'write'])]
    private $updatedAt;

    #[ORM\OneToMany(targetEntity: Sample::class, mappedBy:"sampleType")]
	#[Groups(['read', 'write'])]
    private $samples;

	# background and foreground colors VARCHAR(10)
	#[ORM\Column(type:"string", length:10, nullable:true)]
	#[Groups(['read', 'write'])]
	private $bgColor;

	#[ORM\Column(type:"string", length:10, nullable:true)]
	#[Groups(['read', 'write'])]
	private $fgColor;

	# add quantityType and unitType as strings
	#[ORM\Column(type:"string", length:255, nullable:true)]
	#[Groups(['read', 'write'])]
	private $quantityType;

	#[ORM\Column(type:"string", length:255, nullable:true)]
	#[Groups(['read', 'write'])]
	private $unitType;

	#[ORM\Column(type:"json", nullable:true)]
	#[Groups(['read', 'write'])]
	private $customFields;

	#[ORM\Column(type:"json", nullable:true)]
	private $meta;

    public function __construct()
    {
        $this->samples = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    // Other properties, getters, and setters

    // ... Getters and Setters
	public function getSampleTypeID(): ?int
	{
		return $this->sampleTypeID;
	}

	public function setSampleTypeID(int $sampleTypeID): self
	{
		$this->sampleTypeID = $sampleTypeID;

		return $this;
	}

	public function getMeta()
	{
		$meta = json_encode($this->meta);

		return $meta;
	}

	public function setMeta($meta): self
	{
		if (is_string($meta)) {
			$meta = json_decode($meta, true);
		}
		$this->meta = $meta;
	
		return $this;
	}

	public function getCustomFields()
	{
		$customFields = json_encode($this->customFields);

		return $customFields;
	}

	public function setCustomFields($customFields): self
	{
		if (is_string($customFields)) {
			$customFields = json_decode($customFields, true);
		}
		$this->customFields = $customFields;
	
		return $this;
	}

	public function getQuantityType(): ?string
	{
		return $this->quantityType;
	}

	public function setQuantityType(?string $quantityType): self
	{
		$this->quantityType = $quantityType;

		return $this;
	}

	public function getUnitType(): ?string
	{
		return $this->unitType;
	}

	public function setUnitType(?string $unitType): self
	{
		$this->unitType = $unitType;

		return $this;
	}

	public function getBgColor(): ?string
	{
		return $this->bgColor;
	}

	public function setBgColor(?string $bgColor): self
	{
		$this->bgColor = $bgColor;

		return $this;
	}

	public function getFgColor(): ?string
	{
		return $this->fgColor;
	}

	public function setFgColor(?string $fgColor): self
	{
		$this->fgColor = $fgColor;

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

    #[ORM\PreUpdate]
    #[ORM\PrePersist]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
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

	/**
	 * @return mixed
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * @return mixed
	 */
	public function getUpdatedAt() {
		return $this->updatedAt;
	}
	
	/**
	 * @param mixed $updatedAt 
	 * @return self
	 */
	public function setUpdatedAt($updatedAt): self {
		$this->updatedAt = $updatedAt;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSamples() {
		return $this->samples;
	}
	
	/**
	 * @param mixed $samples 
	 * @return self
	 */
	public function setSamples($samples): self {
		$this->samples = $samples;
		return $this;
	}

	// return toString
	public function __toString(): string
	{
		return $this->name;
	}
}
