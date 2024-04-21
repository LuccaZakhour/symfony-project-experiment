<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SampleSeriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
class SampleSeries
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("read")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read', 'write'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'sampleSeries', targetEntity: Sample::class)]
    #[Groups(['read', 'write'])]
    private Collection $samples;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read', 'write'])]
    private ?string $barcode = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read', 'write'])]
    private \DateTimeInterface $createdAt;

    # set meta
    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups("internal")]
    private ?array $meta = null;

    # user
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'sampleSeries')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['read', 'write'])]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'sampleSeries', targetEntity: Storage::class)]
    #[Groups(['read', 'write'])]
    private Collection $storages;

    public function __construct()
    {
        $this->samples = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->storages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStorages(): Collection
    {
        return $this->storages;
    }
    
    public function addStorage(Storage $storage): self
    {
        if (!$this->storages->contains($storage)) {
            $this->storages[] = $storage;
            $storage->setSampleSeries($this); // Make sure the back-reference is set
        }
        return $this;
    }
    
    public function removeStorage(Storage $storage): self
    {
        if ($this->storages->removeElement($storage)) {
            // set the owning side to null
            if ($storage->getSampleSeries() === $this) {
                $storage->setSampleSeries(null);
            }
        }
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function setMeta(?array $meta): static
    {
        $this->meta = $meta;

        return $this;
    }

    public function getSamples(): Collection
    {
        return $this->samples;
    }

    public function addSample(Sample $sample): static
    {
        if (!$this->samples->contains($sample)) {
            $this->samples[] = $sample;
            $sample->setSampleSeries($this);
        }

        return $this;
    }

    public function removeSample(Sample $sample): static
    {
        if ($this->samples->removeElement($sample)) {
            // set the owning side to null (unless already changed)
            if ($sample->getSampleSeries() === $this) {
                $sample->setSampleSeries(null);
            }
        }

        return $this;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): static
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    # set name, get name
    public function getName(): ?string
    {
        return $this->name;
    }

    # set name, get name
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSampleType(): ?string
    {
        /* @var Sample $sample */
        return $this->getSamples()[0]->getSampleType();
    }

    public function getSampleTypeTitle(): ?string
    {
        $samples = $this->getSamples();
        
        // Check if there are any samples and the first sample is not null
        if (!empty($samples) && $samples[0] !== null) {
            $firstSample = $samples[0];
            $firstSampleType = $firstSample->getSampleType();
    
            // Further check if the sample type is not null
            if ($firstSampleType !== null) {
                $styledTitle = sprintf(
                    "<span style='color: %s; background-color: %s; padding: 5px;'>%s</span>",
                    htmlspecialchars($firstSampleType->getFgColor(), ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($firstSampleType->getBgColor(), ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($firstSampleType->getName(), ENT_QUOTES, 'UTF-8')
                );
                return $styledTitle;
            }
        }
        
        // Return a default or null if there are no samples or the first sample/sample type is null
        return null;
    }
    
}
