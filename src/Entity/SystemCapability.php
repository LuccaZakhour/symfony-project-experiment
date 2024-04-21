<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
class SystemCapability
{
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type:"integer")]
    #[Groups("read")]
    private $id;

    #[ORM\Column(type:"string", length:255)]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(type:"text", nullable:true)]
    #[Groups(['read', 'write'])]
    private $description;

    #[ORM\Column(type:"boolean")]
    #[Groups(['read', 'write'])]
    private $isActive;

    // Other properties, getters, and setters
    public function __construct()
    {
        $this->isActive = false;
    }

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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
