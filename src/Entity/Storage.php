<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiFilter(SearchFilter::class, properties: ['parent' => 'exact', 'id' => 'exact'])]
#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
#[ORM\Entity]
class Storage
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(['read'])]
    private $id;

    #[ORM\Column(length: 255, type: 'string', nullable: true)]
    #[Groups(['internal'])]
    private $storageLayerId;

    #[ORM\Column(length: 255, type: 'string', nullable: true)]
    #[Groups(['read', 'write'])]
    private $barcode;

    #[ORM\Column(length: 255, type: 'string', nullable: true)]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(type:"text", nullable: true)]
    #[Groups(['read', 'write'])]
    private $description;

    #[ORM\Column(type:"json", nullable: true)]
    #[Groups(['internal'])]
    private $meta;

    #[ORM\ManyToOne(targetEntity:"App\Entity\StorageType", inversedBy:"storages")]
    #[ORM\JoinColumn(nullable:true)]
    #[Groups(['read', 'write'])]
    private $storageType;

    #[ORM\OneToMany(targetEntity:"App\Entity\Sample", mappedBy:"storage")]
    #[Groups(['read', 'write'])]
    private $samples;

    #[ORM\Column(type:"string", length: 255, nullable: true)]
    #[Groups(['read', 'write'])]
    private $department;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Experiment", inversedBy: "storages")]
    #[Groups(['read', 'write'])]
    private $experiment;

    // Additional properties, like dimensions, grid type, etc., can be added here
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(['read', 'write'])]
    private $dimensions;

    #[ORM\Column(type:"json", nullable: true)]
    #[Groups(['read', 'write'])]
    private $positionTaken = [];

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'storages')]
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

    /* add
        $meta['address'] = $row['address'];
        $meta['building'] = $row['building'];
        $meta['floor'] = $row['floor'];
        $meta['room'] = $row['room'];
    */
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(['read', 'write'])]
    private $address;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(['read', 'write'])]
    private $building;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(['read', 'write'])]
    private $floor;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(['read', 'write'])]
    private $room;

    #[ORM\ManyToOne(targetEntity: SampleSeries::class, inversedBy: 'storages')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['read', 'write'])]
    private ?SampleSeries $sampleSeries = null;

    public function __construct()
    {
        $this->samples = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the hierarchical storage string.
     *
     * @return string
     */
    public function getHierarchicalStorageString(): string
    {
        $storage = $this;
        $path = [];

        while ($storage !== null) {
            array_unshift($path, $storage->getName()); // Prepend the name of the current storage to the path
            $storage = $storage->getParent(); // Move up to the parent storage
        }

        return implode(' > ', $path); // Join the path parts with a delimiter
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): static
    {
        $this->department = $department;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getBuilding(): ?string
    {
        return $this->building;
    }

    public function setBuilding(?string $building): static
    {
        $this->building = $building;

        return $this;
    }

    public function getFloor(): ?string
    {
        return $this->floor;
    }

    public function setFloor(?string $floor): static
    {
        $this->floor = $floor;

        return $this;
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    public function setRoom(?string $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function setMeta(array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(string $barcode): self
    {
        $this->barcode = $barcode;
        return $this;
    }

    public function getStorageLayerId(): ?string
    {
        return $this->storageLayerId;
    }

    public function setStorageLayerId(string $storageLayerId): self
    {
        $this->storageLayerId = $storageLayerId;
        return $this;
    }
	
    public function getExperiment(): ?Experiment
    {
        return $this->experiment;
    }
    
    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        // add to parent's children
        if ($parent) {
            $parent->addChild($this);
        }
        $this->parent = $parent;
        return $this;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
        }
        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getLevel()
    {
        $level = 0;
        $parent = $this->parent;
        while ($parent) {
            $level++;
            $parent = $parent->getParent();
        }
        return $level;
    }

    public function setExperiment(?Experiment $experiment): self
    {
        $this->experiment = $experiment;

        return $this;
    }

    public function setPositionTaken(array $positionTaken): self
    {
        $this->positionTaken = $positionTaken;
        return $this;
    }

    /**
     * Generates all possible positions as integers for a grid of a specific size.
     * Assumes a default size of 9x9 if not specified.
     *
     * @param int $rows The number of rows in the grid.
     * @param int $columns The number of columns in the grid.
     * @return array An array of integers representing all possible positions in the grid.
     */
    public function generateAllPositionsAsIntArray(): array
    {
        $storageType = $this->getStorageType();
        $positions = $this->generateAllPositions($storageType);

        if (!$positions) {
            return [];
        }

        // Convert "XxY" formatted strings to their numeric equivalents
        $i = 1;
        foreach ($positions as $pos) {
            [$x, $y] = explode('x', $pos);
    
            $numericPositions[] = $i;
            $i++;
        }

        return $numericPositions;
    }

    public function getAvailablePositions()
    {
        $allPositions = $this->generateAllPositionsAsIntArray();
        $takenPositions = $this->getPositionTaken();
        $availablePositions = array_diff($allPositions, $takenPositions);

        $allPositions = array_map('strval', $allPositions);
        $takenPositions = array_map('strval', $takenPositions);
    
        return count($availablePositions);
    }

    // Placeholder method for easy admin
    public function getCols()
    {
        return 0;
    }

    // Placeholder method for easy admin
    public function getRows()
    {
        return 0;
    }

    public function getPositionTaken(): array
    {
        // Assuming $this->positionTaken is already a JSON-decoded array or null
        // Ensure it's always an array, even if null or empty
        return $this->positionTaken ? array_map('strval', $this->positionTaken) : [];
    }
    
	/**
	 * @return mixed
	 */
	public function getName()
    {
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

    public function setDimensions(?string $dimensions): self
    {
        $this->dimensions = $dimensions;
        return $this;
    }

    public function getDimensions(): ?string
    {
        return $this->dimensions;
    }

    public function getColsAndRows(): ?array
    {
        $dimensions = json_decode($this->dimensions, true);
        try {
            $dimensions['rows']['count'];
            $dimensions['columns']['count'];
        } catch (\Exception $e) {
            return null;
        }
        return [$dimensions['rows']['count'], $dimensions['columns']['count']];
    }

	/**
	 * @return mixed
	 */
	public function getStorageType() {
        return $this->storageType;
    }

	/**
	 * @param mixed $storageType 
	 * @return self
	 */
	public function setStorageType($storageType): self {
        $this->storageType = $storageType;
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

	// to string
	public function __toString(): string
    {
        return $this->getName();
    }

    private function generateAllPositions($storageType): ?array
    {
        if ($storageType === null) {
            return $this->generateAllPositionsFixed();
        } else {
            return $this->generateAllPositionsLegacy();
        }
    }

    private function generateAllPositionsLegacy()
    {
        [$rows, $columns] = sscanf($this->dimensions, "%dx%d");

        $this->generateRowsAndColsFromVars($rows, $columns);
    }

    private function generateAllPositionsFixed()
    {
        $dimensions = json_decode($this->dimensions, true);
        
        try {
            $dimensions['rows']['count'];
            $dimensions['columns']['count'];
        } catch (\Exception $e) {
            return null;
        }
        $positions = $this->generateRowsAndColsFromVars($dimensions['rows']['count'], $dimensions['columns']['count']);

        return $positions;
    }

    public function getGenerateAllPositions(): ?array
    {
        return $this->generateAllPositions($this->getStorageType()) ?? null;
    }

    private function generateAllPositionsKeyValue()
    {
        $dimensions = json_decode($this->dimensions, true);
        
        try {
            $dimensions['rows']['count'];
            $dimensions['columns']['count'];
        } catch (\Exception $e) {
            return null;
        }

        /*
        $dimensions = array:2 [▼
            "rows" => array:2 [▼
                "numbering" => "NUMERIC"
                "count" => 8
            ]
            "columns" => array:2 [▼
                "numbering" => "NUMERIC"
                "count" => 8
            ]
            ]
        */
        $values = [];

        for ($row = 1; $row <= $dimensions["rows"]["count"]; $row++) {
            for ($col = 1; $col <= $dimensions["columns"]["count"]; $col++) {
                $values[] = $row + $col;
            }
        }
        return $values;
    }

    private function generateRowsAndColsFromVars($rows, $columns): ?array
    {
        if ($rows === null || $columns === null) {
            return null;
        }

        $positions = [];
        for ($row = 1; $row <= $rows; $row++) {
            for ($col = 1; $col <= $columns; $col++) {
                $positions[] = $row . 'x' . $col;
            }
        }
        return $positions;
    }

    public function getSampleSeries(): ?SampleSeries
    {
        return $this->sampleSeries;
    }

    public function setSampleSeries(?SampleSeries $sampleSeries): static
    {
        $this->sampleSeries = $sampleSeries;

        return $this;
    }
}
