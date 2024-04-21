<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
#[ORM\Entity]
class Study
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

	#[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'studies')]
	#[Groups(['read', 'write'])]
    private $leadResearcher;

	#[ORM\OneToMany(mappedBy: "study", targetEntity: Experiment::class)]
	#[Groups(['read', 'write'])]
	private Collection $experiments;

	#[ORM\ManyToMany(targetEntity: Sample::class, mappedBy: 'studies')]
	#[Groups(['read', 'write'])]
	private Collection $samples;
	
	#[ORM\Column(type:"datetime", nullable:true)]
	#[Groups(['read', 'write'])]
	private $createdAt;

	// set status
	#[ORM\Column(type:"string", nullable:true)]
	#[Groups(['read', 'write'])]
	private $status;

	// set project relationship
	#[ORM\ManyToOne(targetEntity:"App\Entity\Project", inversedBy:"studies")]
	#[Groups(['read', 'write'])]
	private $project;

    public function __construct()
    {
        $this->experiments = new ArrayCollection();
        $this->samples = new ArrayCollection();
		$this->createdAt = new \DateTime();
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

	public function setProject($project): self {
		$this->project = $project;
		return $this;
	}

	public function getProject() {
		return $this->project;
	}

	public function setStatus($status): self {
		$this->status = $status;
		return $this;
	}

	public function getStatus() {
		return $this->status;
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

	public function setCreatedAt($createdAt): self {
		$this->createdAt = $createdAt;
		return $this;
	}

	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * @return mixed
	 */
	public function getLeadResearcher() {
		return $this->leadResearcher;
	}
	
	/**
	 * @param mixed $leadResearcher 
	 * @return self
	 */
	public function setLeadResearcher($leadResearcher): self {
		$this->leadResearcher = $leadResearcher;
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

	/**
	 * @return mixed
	 */
	public function getExperiments() {
		return $this->experiments;
	}
	
	/**
	 * @param mixed $experiments 
	 * @return self
	 */
	public function setExperiments($experiments): self {
		$this->experiments = $experiments;
		return $this;
	}

	public function __toString(): string
	{
		return $this->getName();
	}
}
