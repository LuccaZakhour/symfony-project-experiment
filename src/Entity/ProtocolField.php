<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
class ProtocolField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(['read'])]
    private $id;

    #[ORM\Column(type:"integer")]
    #[Groups(['internal'])]
    private $stepId;

    #[ORM\Column(type:"json", nullable:true)]
    #[Groups(['read', 'write'])]
    private $meta;

    #[ORM\ManyToOne(targetEntity:"Protocol", inversedBy:"fields")]
    #[Groups(['read', 'write'])]
    private $protocol;

    #[ORM\Column(type:"string", nullable: true)]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(type:"text", nullable:true)]
    #[Groups(['read', 'write'])]
    private $value; // Store the value for this field

    #[ORM\Column(type:"integer", nullable:true)]
    private $sortBy;

    // ... (getters and setters)
    public function getId(): ?int
    {
        return $this->id;
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

    public function getProtocol(): ?Protocol
    {
        return $this->protocol;
    }

    public function setProtocol(?Protocol $protocol): self
    {
        $this->protocol = $protocol;

        return $this;
    }

    # set name
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    # get name
    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStepId(): ?int
    {
        return $this->stepId;
    }

    public function setStepId(int $stepId): self
    {
        $this->stepId = $stepId;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getSortBy(): ?int
    {
        return $this->sortBy;
    }

    public function setSortBy(?int $sortBy): self
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    # return toString
    public function __toString(): string
    {
        return $this->name;
    }
}
