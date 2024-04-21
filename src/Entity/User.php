<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use ApiPlatform\Metadata\Get;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    order: ['year' => 'DESC', 'city' => 'ASC'],
    paginationEnabled: false,
    normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']]
)]
#[Get(options: ['test' => 'bar'])]
#[Post()]
#[Put()]
#[GetCollection()]
#[Patch()]
#[Delete()]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ORM\Cache(usage:"NONSTRICT_READ_WRITE", region:"write_rare")]
#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_LAB_MANAGER = 'ROLE_LAB_MANAGER';
    const ROLE_RESEARCHER = 'ROLE_RESEARCHER';
    const ROLE_TECHNICIAN = 'ROLE_TECHNICIAN';

    const ROLE_LAB_TECHNICIAN = 'ROLE_LAB_TECHNICIAN';

    const ROLE_USER = 'ROLE_USER';

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[Groups("read")]
    private ?int $id = null;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(['read', 'write'])]
    private $salutation;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(['read', 'write'])]
    private $firstname;

    #[ORM\Column(type: "string", nullable: true)]
    #[Groups(['read', 'write'])]
    private $lastname;

    #[ORM\Column(type: "string", length: 255, nullable: true, unique: true, options: ["unsigned"=>true])]
    #[Groups(['read', 'write'])]
    private ?string $email = null;

    #[ORM\Column(type: "array", nullable: true)]
    #[Groups(['read', 'write'])]
    private array $roles = [];

    #[ORM\OneToMany(targetEntity:"App\Entity\Storage", mappedBy:"user")]
    #[Groups(['read', 'write'])]
    private $storages;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: "string", nullable: true)]
    #[Groups("internal")]
    private ?string $password = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    #[Groups(['read', 'write'])]
    private $isVerified = false;

    #[ORM\Column(type: "boolean", nullable: true)]
    #[Groups(['read', 'write'])]
    private $enabled;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Groups(['read', 'write'])]
    private $removedAt;

    #[ORM\OneToMany(targetEntity:"App\Entity\Task", mappedBy:"assignedTo")]
    #[Groups(['read', 'write'])]
    private $tasks;
    
    #[ORM\ManyToOne(targetEntity:"App\Entity\Project", inversedBy:"collaborators")]
    #[Groups(['read', 'write'])]
    private $projects;

    #[ORM\ManyToMany(targetEntity:"App\Entity\Group", mappedBy:"users", cascade:["persist"])]
    #[ORM\JoinTable(name:"user_groups")]
    #[ORM\JoinColumn(nullable:true)]
    #[Groups(['read', 'write'])]
    private $groups;

    #[ORM\ManyToMany(targetEntity:"App\Entity\Experiment", mappedBy:"researchers")]
    #[ORM\JoinTable(name:"researcher_experiment")]
    #[ORM\JoinColumn(nullable:true)]
    #[Groups(['read', 'write'])]
    private $experiments;

    #[ORM\OneToMany(targetEntity:"App\Entity\Sample", mappedBy:"user")]
    #[Groups(['read', 'write'])]
    private $samples;

    #[ORM\OneToMany(targetEntity: SampleSeries::class, mappedBy: 'user')]
    #[Groups(['read', 'write'])]
    private $sampleSeries;

    #[ORM\OneToMany(targetEntity: \App\Entity\Experiment::class, mappedBy: "signedBy")]
    #[Groups(['read', 'write'])]
    private $signedExperiments;

    #[ORM\OneToMany(targetEntity:"App\Entity\Order", mappedBy:"orderedBy")]
    #[Groups(['read', 'write'])]

    private $orders;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Protocol::class)]
    #[Groups(['read', 'write'])]
    private Collection $protocols;

    #[ORM\OneToMany(mappedBy: 'leadResearcher', targetEntity: Study::class)]
    #[Groups(['read', 'write'])]
    private Collection $studies;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'user')]
    #[Groups(['read', 'write'])]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'orderedBy', targetEntity: SupplyOrder::class)]
    #[Groups(['read', 'write'])]
    private Collection $supplyOrders;

    #[ORM\OneToMany(mappedBy: "assignedTo", targetEntity: TaskManagement::class)]
    #[Groups(['read', 'write'])]
    private Collection $taskManagements;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->experiments = new ArrayCollection();
        $this->signedExperiments = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->protocols = new ArrayCollection();
        $this->studies = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->supplyOrders = new ArrayCollection();
        $this->taskManagements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjects(): ?Project
    {
        return $this->projects;
    }

    public function setProjects(?Project $projects): self
    {
        $this->projects = $projects;

        return $this;
    }

    public function getSamples(): Collection
    {
        return $this->samples;
    }

    public function getSampleSeries(): Collection
    {
        return $this->sampleSeries;
    }

    public function getTaskManagements(): Collection
    {
        return $this->taskManagements;
    }

    public function addTaskManagement(TaskManagement $taskManagement): self
    {
        if (!$this->taskManagements->contains($taskManagement)) {
            $this->taskManagements[] = $taskManagement;
            $taskManagement->setAssignedTo($this);
        }

        return $this;
    }

    public function removeTaskManagement(TaskManagement $taskManagement): self
    {
        if ($this->taskManagements->removeElement($taskManagement)) {
            // set the owning side to null (unless already changed)
            if ($taskManagement->getAssignedTo() === $this) {
                $taskManagement->setAssignedTo(null);
            }
        }

        return $this;
    }
    
    // Getter for supplyOrders
    public function getSupplyOrders(): Collection
    {
        return $this->supplyOrders;
    }

    // Add a supply order
    public function addSupplyOrder(SupplyOrder $supplyOrder): self
    {
        if (!$this->supplyOrders->contains($supplyOrder)) {
            $this->supplyOrders[] = $supplyOrder;
            $supplyOrder->setOrderedBy($this);
        }

        return $this;
    }

    // Remove a supply order
    public function removeSupplyOrder(SupplyOrder $supplyOrder): self
    {
        if ($this->supplyOrders->removeElement($supplyOrder)) {
            // Set the owning side to null
            if ($supplyOrder->getOrderedBy() === $this) {
                $supplyOrder->setOrderedBy(null);
            }
        }

        return $this;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function getStudies(): Collection
    {
        return $this->studies;
    }

    public function addStudy(Study $study): self
    {
        if (!$this->studies->contains($study)) {
            $this->studies[] = $study;
            $study->setLeadResearcher($this);
        }

        return $this;
    }

    public function removeStudy(Study $study): self
    {
        if ($this->studies->removeElement($study)) {
            // set the owning side to null (unless already changed)
            if ($study->getLeadResearcher() === $this) {
                $study->setLeadResearcher(null);
            }
        }

        return $this;
    }

    public function getProtocols(): Collection
    {
        return $this->protocols;
    }

    public function addProtocol(Protocol $protocol): self
    {
        if (!$this->protocols->contains($protocol)) {
            $this->protocols[] = $protocol;
            $protocol->setUser($this);
        }

        return $this;
    }

    public function removeProtocol(Protocol $protocol): self
    {
        if ($this->protocols->removeElement($protocol)) {
            // set the owning side to null (unless already changed)
            if ($protocol->getUser() === $this) {
                $protocol->setUser(null);
            }
        }

        return $this;
    }

    // Add getter and setter for orders
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setOrderedBy($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // Set the owning side to null (unless it was already changed)
            if ($order->getOrderedBy() === $this) {
                $order->setOrderedBy(null);
            }
        }

        return $this;
    }

    public function getStorages(): Collection
    {
        return $this->storages;
    }

    public function addStorage(Storage $storage): self
    {
        if (!$this->storages->contains($storage)) {
            $this->storages[] = $storage;
            $storage->setUser($this);
        }

        return $this;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        $roles = array_unique($roles);

        // Loop through groups and populate roles
        foreach ($this->getGroups() as $group) {
            // disable adding existing roles
            if (in_array($group->getRole(), $roles)) {
                continue;
            } else {
                $roles[] = $group->getRole();
            }
        }

        return array_unique($roles);
    }

    public function hasRole($role): bool
    {
        if (in_array($role, $this->roles)) {
            return true;
        }

        return false;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    // add role method
    public function addRole($role)
    {
        $this->roles[] = $role;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    // hash password method
    public function hashPassword($passwordHasher)
    {
        $this->password = $passwordHasher->hashPassword($this, $this->password);
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * @param mixed $salutation
     */
    public function setSalutation($salutation): void
    {
        $this->salutation = $salutation;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getRemovedAt()
    {
        return $this->removedAt;
    }

    /**
     * @param mixed $removedAt
     */
    public function setRemovedAt($removedAt): void
    {
        $this->removedAt = $removedAt;
    }

    public function __toString()
    {
        if ($this->email && is_string($this->email)) {
            return $this->email;
        } else if($this->firstname && $this->lastname) {
            return $this->firstname . ' ' . $this->lastname;
        }

        return 'N/A';
    }

	/**
	 * @return mixed
	 */
	public function getGroups() {
		return $this->groups;
	}
	
	/**
	 * @param mixed $groups 
	 * @return self
	 */
	public function setGroups($groups): self {
		$this->groups = $groups;
		return $this;
	}

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;

            $group->addUser($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->removeElement($group)) {

            $group->removeUser($this);
        }

        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getTasks() {
		return $this->tasks;
	}
	
	/**
	 * @param mixed $tasks 
	 * @return self
	 */
	public function setTasks($tasks): self {
		$this->tasks = $tasks;
		return $this;
	}

    /**
     * @return mixed
     */
    public function getSignedExperiments() {
        return $this->signedExperiments;
    }

    /**
     * @param mixed $signedExperiments
     * @return self
     */
    public function setSignedExperiments($signedExperiments): self {
        $this->signedExperiments = $signedExperiments;
        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getExperiments() {
		return $this->experiments;
	}
	
	/**
	 * @param mixed $experiments 
	 * @return self
	 */
	public function setExperiments($experiments): self {
		$this->experiments = $experiments;
		return $this;
	}

    public function addExperiment(Experiment $experiment): self
    {
        if (!$this->experiments->contains($experiment)) {
            $this->experiments[] = $experiment;
            $experiment->addResearcher($this);
        }

        return $this;
    }

    public function removeExperiment(Experiment $experiment): self
    {
        if ($this->experiments->removeElement($experiment)) {
            $experiment->removeResearcher($this);
        }

        return $this;
    }

    public function getFullname()
    {
        return $this->firstname . ' ' . $this->lastname;
    }



    // getSalt and getUsername
    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->email;
    }
}
