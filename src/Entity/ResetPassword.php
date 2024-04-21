<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "reset_password")]
class ResetPassword
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private $id;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private $email;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $otp;

    #[ORM\Column(type: "datetime_immutable", nullable: false)]
    private $createdAt;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private $expiresAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getOtp(): ?string
    {
        return $this->otp;
    }

    public function setOtp(string $otp): self
    {
        $this->otp = $otp;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param mixed $expiresAt
     */
    public function setExpiresAt($expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }
}