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
class TaskManagement
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
	#[Groups("read")]
    private $id;

    #[ORM\Column(length: 255, type: 'string')]
	#[Groups(['read', 'write'])]
    private $title;

    #[ORM\Column(nullable: true, type: 'text')]
	#[Groups(['read', 'write'])]
    private $description;

    #[ORM\Column(type:"datetime", nullable:true)]
	#[Groups(['read', 'write'])]
    private $dueDate;

    #[ORM\ManyToOne(targetEntity:"App\Entity\User", inversedBy:"taskManagements")]
	#[Groups(['read', 'write'])]
    private $assignedTo;

    #[ORM\ManyToMany(targetEntity:"App\Entity\Sample", mappedBy:"taskManagements")]
	#[Groups(['read', 'write'])]
    private $samples;

    #[ORM\ManyToMany(targetEntity:"App\Entity\Experiment", mappedBy:"taskManagements")]
	#[Groups(['read', 'write'])]
    private $experiments;

    // status
    #[ORM\Column(length: 255, type: 'string')]
	#[Groups(['read', 'write'])]
    private $status;

    // Other properties, getters, and setters

    public function __construct()
    {
        $this->samples = new ArrayCollection();
        $this->experiments = new ArrayCollection();
    }

    // ... Getters and Setters

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
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * @param mixed $title 
	 * @return self
	 */
	public function setTitle($title): self {
		$this->title = $title;
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

	/**
	 * @return mixed
	 */
	public function getDueDate() {
		return $this->dueDate;
	}
	
	/**
	 * @param mixed $dueDate 
	 * @return self
	 */
	public function setDueDate($dueDate): self {
		$this->dueDate = $dueDate;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAssignedTo() {
		return $this->assignedTo;
	}
	
	/**
	 * @param mixed $assignedTo 
	 * @return self
	 */
	public function setAssignedTo($assignedTo): self {
		$this->assignedTo = $assignedTo;
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

	public function addSample(Sample $sample): self
	{
		if (!$this->samples->contains($sample)) {
			$this->samples[] = $sample;
			$sample->addTaskManagement($this);
		}

		return $this;
	}

	public function removeSample(Sample $sample): self
	{
		if ($this->samples->contains($sample)) {
			$this->samples->removeElement($sample);
			$sample->removeTaskManagement($this);
		}

		return $this;
	}

	public function addExperiment(Experiment $experiment): self
	{
		if (!$this->experiments->contains($experiment)) {
			$this->experiments[] = $experiment;
			$experiment->addTaskManagement($this);
		}

		return $this;
	}

	public function removeExperiment(Experiment $experiment): self
	{
		if ($this->experiments->contains($experiment)) {
			$this->experiments->removeElement($experiment);
			$experiment->removeTaskManagement($this);
		}

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
}
