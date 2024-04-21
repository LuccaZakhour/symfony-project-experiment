<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
#[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
#[ORM\Table(name: "projects")]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups("read")]
    private $id;

    #[ORM\Column(type: "string")]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(type: "string")]
    #[Groups(['read', 'write'])]
    private $shortName;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Group", inversedBy: "projects")]
    #[Groups(['read', 'write'])]
    private $group;

    #[ORM\Column(type: "text")]
    #[Groups(['read', 'write'])]
    private $description;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(['read', 'write'])]
    private $notes;
    
     #[ORM\OneToMany(targetEntity:"App\Entity\User", mappedBy:"projects")]
     #[ORM\JoinColumn(nullable:true)]
     #[Groups(['read', 'write'])]
    private $collaborators;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(['read', 'write'])]
    private $status;

    #[ORM\Column(type: "datetime")]
    #[Groups(['read', 'write'])]
    private $createdAt;

    // set studies
    #[ORM\OneToMany(targetEntity:"App\Entity\Study", mappedBy:"project")]
    #[ORM\JoinColumn(nullable:true)]
    #[Groups(['read', 'write'])]
    private $studies;

    // set experiments
    #[ORM\OneToMany(targetEntity:"App\Entity\Experiment", mappedBy:"project")]
    #[ORM\JoinColumn(nullable:true)]
    #[Groups(['read', 'write'])]
    private $experiments;

    public function __construct()
    {
        $this->collaborators = new ArrayCollection();
        //$this->labels = new ArrayCollection();
        $this->studies = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function getStudies() {
        return $this->studies;
    }

    public function setStudies($studies) {
        $this->studies = $studies;
    }

    public function addStudy($study) {
        $this->studies[] = $study;
    }

    public function removeStudy($study) {
        $this->studies->removeElement($study);
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    // Getter and setter for $name
    public function getName() {
        return $this->name;
    }
    public function setName($name) {
        $this->name = $name;
    }

    // Getter and setter for $group
    public function getGroup() {
        return $this->group;
    }
    public function setGroup($group) {
        $this->group = $group;
    }

    public function setShortName($shortName) {
        $this->shortName = $shortName;
    }

    public function getShortName() {
        return $this->shortName;
    }

    public function getDescription() {
        return $this->description;
    }
    public function setDescription($description) {
        $this->description = $description;
    }

    // Getter and setter for $notes
    public function getNotes() {
        return $this->notes;
    }
    public function setNotes($notes) {
        $this->notes = $notes;
    }

    // Getter and setter for $collaborators
    public function getCollaborators() {
        return $this->collaborators;
    }
    public function setCollaborators($collaborators) {
        $this->collaborators = $collaborators;
    }

    public function addCollaborator($collaborator) {
        $this->collaborators[] = $collaborator;
    }

    // Getter and setter for $status
    public function getStatus() {
        return $this->status;
    }
    public function setStatus($status) {
        $this->status = $status;
    }

    // toString
    public function __toString(): string
    {
        return $this->getName();
    }
}
