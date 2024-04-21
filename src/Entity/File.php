<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
#[ORM\Entity]
class File
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
	#[Groups(['read'])]
    private $id;

	#[ORM\Column(type: "integer", nullable: true)]
	#[Groups(['internal'])]
	private $expJournalID;

	#[ORM\Column(type:"json", nullable:true)]
	#[Groups(['internal'])]
	private $meta;

	#[ORM\Column(type: "integer", nullable: true)]
	#[Groups(['internal'])]
	private $experimentFileID;

    #[ORM\Column(length: 255, type: 'string')]
	#[Groups(['read', 'write'])]
    private $filename;

    // create $filesize property here
    #[ORM\Column(type:"integer")]
	#[Groups(['read', 'write'])]
    private $filesize;
    
    #[ORM\Column(length: 255, type: 'string', nullable: true)]
	#[Groups(['read', 'write'])]
    private $description;

    #[ORM\Column(length: 255, type: 'string')]
	#[Groups(['read', 'write'])]
    private $filetype;

    #[ORM\Column(type:"text", nullable: true)]
	#[Groups(['read', 'write'])]
    private $filepath;

    #[ORM\Column(type:"text", nullable: true)]
	#[Groups(['read', 'write'])]
	private $fullFilePath;

    #[ORM\ManyToOne(targetEntity:"App\Entity\Experiment", inversedBy:"files")]
	#[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
	#[ORM\JoinColumn(name:"experiment_id", referencedColumnName:"id", nullable:true, onDelete:"SET NULL")]
	#[Groups(['read', 'write'])]
    private $experiment;

	#[ORM\ManyToOne(inversedBy: 'files', targetEntity: Protocol::class)]
	#[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
	#[Groups(['read', 'write'])]
    private ?Protocol $protocol = null;

    #[ORM\ManyToOne(targetEntity:"App\Entity\Sample", inversedBy:"files")]
	#[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
	#[Groups(['read', 'write'])]
    private $sample;

	#[ORM\ManyToOne(targetEntity: Section::class, inversedBy: "files")]
	#[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[ORM\JoinColumn(name: "experiment_section_id", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
	#[Groups(['read', 'write'])]
    private ?Section $experimentSection = null;

	#[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
	private $createdAt;

	public function __construct() {
		$this->createdAt = new \DateTime();
	}

    // Other properties, getters, and setters

    // ... Getters and Setters

	public function getExperimentFileID() {
		return $this->experimentFileID;
	}

	public function setExperimentFileID($experimentFileID): self {
		$this->experimentFileID = $experimentFileID;
		return $this;
	}

	public function getExpJournalID() {
		return $this->expJournalID;
	}

	public function setExpJournalID($expJournalID): self {
		$this->expJournalID = $expJournalID;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFilename(): string {
		return $this->filename;
	}
	
	/**
	 * @param mixed $filename 
	 * @return self
	 */
	public function setFilename(string $filename): self {
		$this->filename = $filename;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFullFilePath() {
		return $this->fullFilePath;
	}

	/**
	 * @param mixed $fullFilePath 
	 * @return self
	 */
	public function setFullFilePath($fullFilePath): self {
		$this->fullFilePath = $fullFilePath;
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getFilesize() {
		return $this->filesize;
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

	public function getExperimentSection(): ?Section
    {
        return $this->experimentSection;
    }

    public function setExperimentSection(?Section $experimentSection): self
    {
        $this->experimentSection = $experimentSection;
        return $this;
    }
	
	/**
	 * @param mixed $filesize 
	 * @return self
	 */
	public function setFilesize($filesize): self {
		$this->filesize = $filesize;
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
	public function getExperiment() {
		return $this->experiment;
	}
	
	/**
	 * @param mixed $experiment 
	 * @return self
	 */
	public function setExperiment($experiment): self {
		$this->experiment = $experiment;
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
	public function getFiletype() {
		return $this->filetype;
	}
	
	/**
	 * @param mixed $filetype 
	 * @return self
	 */
	public function setFiletype($filetype): self {
		$this->filetype = $filetype;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFilepath() {
		return $this->filepath;
	}
	
	/**
	 * @param mixed $filepath 
	 * @return self
	 */
	public function setFilepath($filepath): self {
		$this->filepath = $filepath;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getProtocol() {
		return $this->protocol;
	}
	
	/**
	 * @param mixed $protocol 
	 * @return self
	 */
	public function setProtocol($protocol): self {
		$this->protocol = $protocol;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSample() {
		return $this->sample;
	}
	
	/**
	 * @param mixed $sample 
	 * @return self
	 */
	public function setSample($sample): self {
		$this->sample = $sample;
		return $this;
	}

	# to string
	public function __toString() {
		return $this->filename ?? 'n/a';
	}
}
