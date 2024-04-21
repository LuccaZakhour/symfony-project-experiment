<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
#[ORM\Entity]
class Section
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(['read'])]
    private $id;

    #[ORM\Column(nullable: true, type: 'integer')]
    #[Groups(['internal'])]
    private $expJournalId;

    #[ORM\Column(nullable: true, type: 'json')]
    #[Groups(['read', 'write'])]
    private $meta;

    #[ORM\Column(nullable: true, type: 'text')]
    #[Groups(['read', 'write'])]
    private $origMeta;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(nullable: true, type: 'text')]
    #[Groups(['read', 'write'])]
    private $description;

    #[ORM\Column(nullable: true, type: 'string')]
    #[Groups(['read', 'write'])]
    private $type;

    #[ORM\ManyToOne(targetEntity: Experiment::class, inversedBy:"sections")]
    #[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[Groups(['read', 'write'])]
    private $experiment;

    #[ORM\OneToMany(targetEntity: Sample::class, mappedBy:"section")]
    #[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[Groups(['read', 'write'])]
    private $samples;

    #[ORM\OneToMany(mappedBy: "experimentSection", targetEntity: File::class)]
    #[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
    #[Groups(['read', 'write'])]
    private Collection $files;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['read', 'write'])]
    private $createdAt;
    
    public function __construct()
    {
        $this->samples = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->files = new ArrayCollection();
    }

    // Other properties, getters, and setters
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setExperimentSection($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getExperimentSection() === $this) {
                $file->setExperimentSection(null);
            }
        }

        return $this;
    }

    public function addSample(Sample $sample): self
    {
        if (!$this->samples->contains($sample)) {
            $this->samples[] = $sample;
            $sample->setSection($this);
        }
        return $this;
    }

    public function removeSample(Sample $sample): self
    {
        if ($this->samples->contains($sample)) {
            $this->samples->removeElement($sample);
            // set the owning side to null (unless already changed)
            if ($sample->getSection() === $this) {
                $sample->setSection(null);
            }
        }
        return $this;
    }

    public function getSamples()
    {
        return $this->samples;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function setMeta(?array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    public function getOrigMeta(): ?string
    {
        return $this->origMeta;
    }

    public function setOrigMeta(?string $origMeta): self
    {
        // if is array, convert to string
        if (is_array($origMeta)) {
            $origMeta = json_encode($origMeta);
        }
        $this->origMeta = $origMeta;
        return $this;
    }

    public function getExpJournalId(): ?int
    {
        return $this->expJournalId;
    }

    public function setExpJournalId(?int $expJournalId): self
    {
        $this->expJournalId = $expJournalId;
        return $this;
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

    public function getExperiment(): ?Experiment
    {
        return $this->experiment;
    }

    public function setExperiment(?Experiment $experiment): self
    {
        $this->experiment = $experiment;
        return $this;
    }
    
    // return toString
    public function __toString(): string
    {
        return $this->getName();
    }
}
