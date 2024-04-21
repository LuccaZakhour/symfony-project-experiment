<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
#[ORM\Entity]
class Sample
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(['read'])]
    private $id;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(type:"json", nullable:true)]
    #[Groups(['read', 'write'])]
    private $meta;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    #[Groups(['internal'])]
    private $sampleID;

    #[ORM\Column(type:"text", nullable:true)]
    #[Groups(['read', 'write'])]
    private $description;

    #[ORM\Column(type:"datetime")]
    #[Groups(['read', 'write'])]
    private $createdAt;

    #[ORM\Column(type:"datetime")]
    #[Groups(['read', 'write'])]
    private $updatedAt;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    #[Groups(['read', 'write'])]
    private $barcode;

    #[ORM\ManyToOne(targetEntity:"App\Entity\SampleType", inversedBy:"samples")]
    #[Groups(['read', 'write'])]
    private $sampleType;

    #[ORM\ManyToMany(targetEntity: Task::class, mappedBy: 'samples')]
    #[Groups(['read', 'write'])]
    private Collection $tasks;    

    #[ORM\ManyToOne(targetEntity: Storage::class, inversedBy: 'samples')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['read', 'write'])]
    private $storage;

    #[ORM\ManyToMany(targetEntity: Study::class, inversedBy: 'samples')]
    #[ORM\JoinTable(name: "study_sample")]
    #[Groups(['read', 'write'])]
    private Collection $studies;
	
    #[ORM\Column(type:"string", length:10, nullable:true)]
    #[Groups(['read', 'write'])]
    private $position;

    #[ORM\ManyToOne(targetEntity: Section::class, inversedBy: 'samples')]
    #[ORM\JoinColumn(name:"section_id", referencedColumnName:"id", nullable:true, onDelete:"SET NULL")]
    #[Groups(['read', 'write'])]
    private $section;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'samples')]
    #[Groups(['read', 'write'])]
    private $user;

    # onDelete do nothing because we want to keep the children
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Groups(['read', 'write'])]
    private $parent;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    #[Groups(['read', 'write'])]
    private $children;

    #[ORM\ManyToOne(targetEntity: SampleSeries::class, inversedBy: 'samples')]
    #[Groups(['read', 'write'])]
    private $sampleSeries;

    private $sampleCounts;
    #[ORM\Column(type: "json", nullable: true)]
    #[Groups(['read', 'write'])]
    private $customFieldValues;

    #[ORM\OneToMany(targetEntity:"App\Entity\File", mappedBy:"sample")]
    #[Groups(['read', 'write'])]

    private $files;

    #[ORM\ManyToMany(targetEntity: TaskManagement::class, inversedBy: "samples")]
    #[Groups(['read', 'write'])]
    private Collection $taskManagements;
    
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->tasks = new ArrayCollection();
        $this->studies = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->taskManagements = new ArrayCollection();
    }
    
     const SAMPLE_TYPE_TISSUE = 'SAMPLE_TYPE_TISSUE';
     const SAMPLE_TYPE_BLOOD = 'SAMPLE_TYPE_BLOOD';
     const SAMPLE_TYPE_FLUID = 'SAMPLE_TYPE_FLUID';
     const SAMPLE_TYPE_CELL_CULTURE = 'SAMPLE_TYPE_CELL_CULTURE';
     const SAMPLE_TYPE_PLANT = 'SAMPLE_TYPE_PLANT';
     const SAMPLE_TYPE_SOIL = 'SAMPLE_TYPE_SOIL';
     const SAMPLE_TYPE_WATER = 'SAMPLE_TYPE_WATER';
     const SAMPLE_TYPE_FOOD = 'SAMPLE_TYPE_FOOD';
     const SAMPLE_TYPE_DRUG = 'SAMPLE_TYPE_DRUG';
     const SAMPLE_TYPE_METAL = 'SAMPLE_TYPE_METAL';
     const SAMPLE_TYPE_MINERAL = 'SAMPLE_TYPE_MINERAL';
     const SAMPLE_TYPE_POLYMER = 'SAMPLE_TYPE_POLYMER';
     const SAMPLE_TYPE_COMPOSITE = 'SAMPLE_TYPE_COMPOSITE';
     const SAMPLE_TYPE_ENVIRONMENTAL = 'SAMPLE_TYPE_ENVIRONMENTAL';
     const SAMPLE_TYPE_OTHER = 'SAMPLE_TYPE_OTHER';
    // Other properties, getters, and setters

    // ... Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskManagements(): Collection
    {
        return $this->taskManagements;
    }

    public function addTaskManagement(TaskManagement $taskManagement): self
    {
        if (!$this->taskManagements->contains($taskManagement)) {
            $this->taskManagements[] = $taskManagement;
        }

        return $this;
    }

    public function removeTaskManagement(TaskManagement $taskManagement): self
    {
        $this->taskManagements->removeElement($taskManagement);

        return $this;
    }
    
    // Add task
    public function addTask(Task $task): self {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->addSample($this); // Ensure the bidirectional link
        }
        return $this;
    }

    // Remove task
    public function removeTask(Task $task): self {
        if ($this->tasks->removeElement($task)) {
            $task->removeSample($this); // Break the bidirectional link
        }
        return $this;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setSample($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getSample() === $this) {
                $file->setSample(null);
            }
        }

        return $this;
    }

    public function getCustomFieldValues(): ?array
    {
        return $this->customFieldValues;
    }

    public function setCustomFieldValues(?array $customFieldValues): self
    {
        $this->customFieldValues = $customFieldValues;

        return $this;
    }

    # sample series get set
    public function getSampleSeries(): ?SampleSeries
    {
        return $this->sampleSeries;
    }

    # sample series get set
    public function setSampleSeries(?SampleSeries $sampleSeries): self
    {
        $this->sampleSeries = $sampleSeries;

        return $this;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function setMeta(?array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    # set, get parent
    public function getParent(): ?self
    {
        return $this->parent;
    }

    # set, get parent
    public function setParent(?self $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    # set, get children
    public function getChildren(): Collection
    {
        return $this->children;
    }

    # set, get children
    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
        }
        return $this;
    }

    # set, get user
    public function getUser(): ?User
    {
        return $this->user;
    }

    # set, get user
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    # set, get name
    public function getSampleID(): ?string
    {
        return $this->sampleID;
    }

    public function setSampleID(?string $sampleID): self
    {
        $this->sampleID = $sampleID;
        return $this;
    }

    # set, get description
    public function getDescription(): ?string
    {
        return $this->description;
    }

    # set, get description
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;
        return $this;
    }
    
    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): self
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function getStudies(): Collection
    {
        return $this->studies;
    }

    public function addStudy(Study $study): self
    {
        if (!$this->studies->contains($study)) {
            $this->studies[] = $study;
        }

        return $this;
    }

    public function getSampleType(): ?SampleType
    {
        return $this->sampleType;
    }

    public function setSampleType(?SampleType $sampleType): self
    {
        $this->sampleType = $sampleType;

        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getPosition() {
		return $this->position;
	}

	/**
	 * @param mixed $position 
	 * @return self
	 */
	public function setPosition($position): self {
		$this->position = $position;

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

    #[ORM\PreUpdate]
    #[ORM\PrePersist]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // return toString
    public function __toString(): string
    {
        return $this->name;
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

    public function getStorage()
    {
        return $this->storage;
    }

    public function setStorage($storage): self
    {
        $this->storage = $storage;

        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getTasks() {
		return $this->tasks;
	}
	
	/**
	 * @param mixed $tasks 
	 * @return self
	 */
	public function setTasks($tasks): self {
		$this->tasks = $tasks;
		return $this;
    }

    /**
     * @return mixed
     */
    public function getSampleCounts() {
        return $this->sampleCounts;
    }

    /**
     * @param mixed $sampleCounts
     * @return self
     */
    public function setSampleCounts($sampleCounts): self {
        $this->sampleCounts = $sampleCounts;
        return $this;
    }
}
