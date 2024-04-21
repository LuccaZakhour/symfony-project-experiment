<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
class TimeZone
{
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type:"integer")]
    #[Groups("read")]
    private $id;

    #[ORM\Column(type:"string", length:100)]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(type:"string", length:10)]
    #[Groups(['read', 'write'])]
    private $offset;

    #[ORM\Column(type:"boolean")]
    #[Groups(['read', 'write'])]
    private $isDefault;

    // Other properties, getters, and setters

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

    public function getOffset(): ?string
    {
        return $this->offset;
    }

    public function setOffset(string $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function getIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }
}
