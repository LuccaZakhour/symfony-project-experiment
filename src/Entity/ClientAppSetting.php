<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
class ClientAppSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups("read")]
    private $id;

    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(type:"text")]
    #[Groups(['read', 'write'])]
    private $value;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    #[Groups(['read', 'write'])]
    private $description;

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): ClientAppSetting
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name ?? '';
    }

    public function setName(string $name): ClientAppSetting
    {
        $this->name = $name;
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value ?? '';
    }

    public function setValue(string $value): ClientAppSetting
    {
        $this->value = $value;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description ?? '';
    }

    public function setDescription(string $description): ClientAppSetting
    {
        $this->description = $description;
        return $this;
    }
}
