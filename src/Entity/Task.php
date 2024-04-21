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
class Task
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
	#[Groups("read")]
    private $id;

    #[ORM\Column(length: 255, type: 'string')]
	#[Groups(['read', 'write'])]
    private $title;

    #[ORM\Column(type:"text")]
	#[Groups(['read', 'write'])]

    private $description;

    #[ORM\Column(type:"string", length:50)]
	#[Groups(['read', 'write'])]

    private $status;

    #[ORM\Column(type:"datetime", nullable:true)]
	#[Groups(['read', 'write'])]

    private $dueDate;

    #[ORM\ManyToOne(targetEntity:"App\Entity\User", inversedBy:"tasks")]
	#[Groups(['read', 'write'])]

    private $assignedTo;

    #[ORM\ManyToMany(targetEntity:"App\Entity\Experiment", inversedBy:"tasks")]
	#[ORM\JoinColumn(nullable: true)]
	#[Groups(['read', 'write'])]

    private $experiments;

    #[ORM\ManyToMany(targetEntity: Sample::class, inversedBy: 'tasks')]
	#[ORM\JoinTable(name: "task_sample")]
	#[Groups(['read', 'write'])]

    private $samples;

    #[ORM\ManyToMany(targetEntity:"App\Entity\Protocol", inversedBy:"tasks")]
	#[Groups(['read', 'write'])]

    private $protocols;

	const TASK_STATUS_TODO = 'To Do';
    const TASK_STATUS_IN_PROGRESS = 'In Progress';
    const TASK_STATUS_DONE = 'Done';
    const TASK_STATUS_ON_HOLD = 'On Hold';
    const TASK_STATUS_CANCELLED = 'Cancelled';


    public function __construct()
    {
        $this->experiments = new ArrayCollection();
        $this->samples = new ArrayCollection();
        $this->protocols = new ArrayCollection();
    }

    // Other properties, getters, and setters

    // ... Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
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
	public function getProtocols() {
		return $this->protocols;
	}
	
	/**
	 * @param mixed $protocols 
	 * @return self
	 */
	public function setProtocols($protocols): self {
		$this->protocols = $protocols;
		return $this;
	}

    public function addExperiment(Experiment $experiment): self
    {
        if (!$this->experiments->contains($experiment)) {
            $this->experiments[] = $experiment;
        }

        return $this;
    }

    public function removeExperiment(Experiment $experiment): self
    {
        $this->experiments->removeElement($experiment);

        return $this;
    }

    public function addSample(Sample $sample): self
    {
        if (!$this->samples->contains($sample)) {
            $this->samples[] = $sample;
        }

        return $this;
    }

	public function removeSample(Sample $sample): self {
		if ($this->samples->removeElement($sample)) {
			$sample->removeTask($this); // Break the bidirectional link
		}
		return $this;
	}

    public function addProtocol(Protocol $protocol): self
    {
        if (!$this->protocols->contains($protocol)) {
            $this->protocols[] = $protocol;
        }

        return $this;
    }

    public function removeProtocol(Protocol $protocol): self
    {
        $this->protocols->removeElement($protocol);

        return $this;
    }

	// toString
	public function __toString() {
		return $this->title;
	}
}
