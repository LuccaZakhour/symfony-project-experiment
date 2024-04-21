<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
#[ORM\Entity]
class Experiment
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(['read'])]
    private $id;

    # experimentID
    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['internal'])]
    private $experimentID;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(nullable: true, type: 'text')]
    #[Groups(['read', 'write'])]
    private $description;

    #[ORM\ManyToOne(targetEntity:"App\Entity\Protocol", inversedBy:"experiments")]
    #[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[ORM\JoinColumn(nullable:true)]
    #[Groups(['read', 'write'])]
    private $protocol;

    #[ORM\OneToMany(targetEntity: Section::class, mappedBy:"experiment", cascade:["persist"])]
    #[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[Groups(['read', 'write'])]
    private $sections;

    #[ORM\ManyToMany(targetEntity:"App\Entity\User", inversedBy:"experiments")]
    #[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    private $researchers;

    #[ORM\ManyToMany(targetEntity: Task::class, mappedBy:"experiments")]
    #[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[Groups(['read', 'write'])]
    private $tasks;

    #[ORM\ManyToOne(targetEntity: Study::class, inversedBy: "experiments")]
    #[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[Groups(['read', 'write'])]
    private $study;
    
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy:"experiments")]
    #[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[Groups(['read', 'write'])]
    private $project;

    ## add createdAt
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['read', 'write'])]
    private $createdAt;

    # add status
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read', 'write'])]
    private $status;

    # on delete ignore
    #[ORM\OneToMany(targetEntity: File::class, mappedBy:"experiment", cascade:["persist"])]
    #[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[Groups(['read', 'write'])]
    private $files;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class, inversedBy: "signedExperiments")]
    #[ORM\JoinColumn(name: "signedBy", referencedColumnName: "id")]
    #[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[Groups(['read', 'write'])]
    private $signedBy;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    #[Groups(['read', 'write'])]
    private $signedAt;

    #[ORM\OneToMany(mappedBy: "experiment", targetEntity: Storage::class)]
    #[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[Groups(['read', 'write'])]
    private Collection $storages;

    #[ORM\ManyToMany(targetEntity: TaskManagement::class, inversedBy: "experiments")]
    #[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[Groups(['read', 'write'])]
    private Collection $taskManagements;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->researchers = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->storages = new ArrayCollection();
        $this->taskManagements = new ArrayCollection();
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
        if ($this->taskManagements->contains($taskManagement)) {
            $this->taskManagements->removeElement($taskManagement);
        }

        return $this;
    }

    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
        }

        return $this;
    }	

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
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
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
        }

        return $this;
    }

    public function getStorages(): Collection
    {
        return $this->storages;
    }

    public function addStorage(Storage $storage): self
    {
        if (!$this->storages->contains($storage)) {
            $this->storages[] = $storage;
        }

        return $this;
    }

    public function removeStorage(Storage $storage): self
    {
        if ($this->storages->contains($storage)) {
            $this->storages->removeElement($storage);
        }

        return $this;
    }

    public function getExperimentID(): ?int
    {
        return $this->experimentID;
    }

    public function setExperimentID(int $experimentID): self
    {
        $this->experimentID = $experimentID;

        return $this;
    }

    public function getProtocol(): ?Protocol
    {
        return $this->protocol;
    }

    public function setProtocol(Protocol $protocol): self
    {
        $this->protocol = $protocol;        

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;        

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setStudy(Study $study): self
    {
        $this->study = $study;        

        return $this;
    }

    public function getStudy(): ?Study
    {
        return $this->study;
    }

    public function setSignedBy(User $signedBy): self
    {
        $this->signedBy = $signedBy;

        return $this;
    }

    public function getSignedBy(): ?User
    {
        return $this->signedBy;
    }

    public function getSignedAt(): ?\DateTimeInterface
    {
        return $this->signedAt;
    }

    public function setSignedAt(): self
    {
        $this->signedAt = new \DateTimeImmutable();

        return $this;
    }

    public function addResearcher(User $researcher): self
    {
        if (!$this->researchers->contains($researcher)) {
            $this->researchers[] = $researcher;
        }

        return $this;
    }

    public function removeResearcher(User $researcher): self
    {
        if ($this->researchers->contains($researcher)) {
            $this->researchers->removeElement($researcher);
        }

        return $this;
    }

    // Getters and Setters
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Section[]
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections[] = $section;
            $section->setExperiment($this);
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        if ($this->sections->contains($section)) {
            $this->sections->removeElement($section);
            // set the owning side to null
            if ($section->getExperiment() === $this) {
                $section->setExperiment(null);
            }
        }

        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getResearchers() {
		return $this->researchers;
	}

    // toString
    public function __toString(): string
    {
        return $this->name;
    }
}
