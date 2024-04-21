<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\VariableRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity(repositoryClass: VariableRepository::class)]
class Variable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(type: "integer")]
    #[Groups(['internal'])]
    private $varID;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(type:"text", nullable: true)]
    #[Groups(['read', 'write'])]
    private $description;

    #[ORM\Column(type:"json")]
    #[Groups(['read', 'write'])]
    private $meta;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $type;

    #[ORM\Column(length: 255, type: 'string', nullable: true)]
    #[Groups(['read', 'write'])]
    private $unit;

    #[ORM\Column(length: 255, type: 'string', nullable: true)]
    #[Groups(['read', 'write'])]
    private $contents;
 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getVarID(): int
    {
        return $this->varID;
    }

    public function setVarID(int $varID): static
    {
        $this->varID = $varID;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function setContents($contents): static
    {
        $this->contents = $contents;

        return $this;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function setMeta(array $meta): static
    {
        $this->meta = $meta;

        return $this;
    }
}
