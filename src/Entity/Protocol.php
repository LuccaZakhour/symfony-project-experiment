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
class Protocol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
	#[Groups(['read'])]
    private $id;

	#[ORM\Column(type:"integer", nullable:true)]
	#[Groups(['internal'])]
	private $protId;

    #[ORM\Column(length: 255, type: 'string')]
	#[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(type:"text", nullable:true)]
	#[Groups(['read', 'write'])]
    private $description;

	# add category string
	#[ORM\Column(length: 255, type: 'string', nullable:true)]
	#[Groups(['read', 'write'])]
	private $category;

	#[ORM\OneToMany(targetEntity:"ProtocolField", mappedBy:"protocol", cascade: ["persist", "remove"])]
    #[ORM\OrderBy(['sortBy' => 'ASC'])]
	#[Groups(['read', 'write'])]
    private $fields;

	# add experiments
	#[ORM\OneToMany(targetEntity:"App\Entity\Experiment", mappedBy:"protocol")]
	#[Groups(['read', 'write'])]
	private $experiments;

	#[ORM\Column(type:"json", nullable:true)]
	#[Groups(['internal'])]
	private $meta;

	#[ORM\ManyToOne(targetEntity:"App\Entity\User", inversedBy:"protocols")]
	#[Groups(['read', 'write'])]
	private $user;

	#[ORM\ManyToMany(targetEntity: "App\Entity\Task", mappedBy: "protocols")]
	#[Groups(['read', 'write'])]
	private $tasks;

    #[ORM\OneToMany(mappedBy: 'protocol', targetEntity: File::class, cascade: ['persist', 'remove'])]
	#[Groups(['read', 'write'])]
    private Collection $files;

	public function __construct()
	{
		$this->fields = new ArrayCollection();
		$this->tasks = new ArrayCollection();
		$this->files = new ArrayCollection();
	}

	# getter setters
	public function getFiles(): Collection
	{
		return $this->files;
	}

	public function addFile(File $file): self
	{
		if (!$this->files->contains($file)) {
			$this->files[] = $file;
			$file->setProtocol($this);
		}

		return $this;
	}

	public function removeFile(File $file): self
	{
		if ($this->files->removeElement($file)) {
			// set the owning side to null (unless already changed)
			if ($file->getProtocol() === $this) {
				$file->setProtocol(null);
			}
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
			$task->addProtocol($this);
		}

		return $this;
	}

	public function removeTask(Task $task): self
	{
		if ($this->tasks->removeElement($task)) {
			$task->removeProtocol($this);
		}

		return $this;
	}

	# getter setters
	public function getProtId(): ?int
	{
		return $this->protId;
	}

	public function setProtId(int $protId): self
	{
		$this->protId = $protId;

		return $this;
	}

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(?User $user): self
	{
		$this->user = $user;

		return $this;
	}

	# getter setters
	public function getMeta(): ?array
	{
		return $this->meta;
	}

	public function setMeta(?array $meta): self
	{
		$this->meta = $meta;

		return $this;
	}

	public function getCategory(): string
	{
		return $this->category;
	}

	public function setCategory(string $category): self
	{
		$this->category = $category;

		return $this;
	}

	# getter setters
	public function getExperiments(): Collection
	{
		return $this->experiments;
	}

	public function addExperiment(Experiment $experiment): self
	{
		if (!$this->experiments->contains($experiment)) {
			$this->experiments[] = $experiment;
			$experiment->setProtocol($this);
		}

		return $this;
	}

	public function removeExperiment(Experiment $experiment): self
	{
		if ($this->experiments->removeElement($experiment)) {
			// set the owning side to null (unless already changed)
			if ($experiment->getProtocol() === $this) {
				$experiment->setProtocol(null);
			}
		}

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
	 * @return Collection|ProtocolField[]
	 */
	public function getFields(): Collection {
		return $this->fields;
	}

	public function addField(ProtocolField $field): self {
		if (!$this->fields->contains($field)) {
			$this->fields[] = $field;
			$field->setProtocol($this);
		}

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

	// return toString
	public function __toString(): string
	{
		return $this->name;
	}
}
