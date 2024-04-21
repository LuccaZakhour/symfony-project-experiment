<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(normalizationContext: ['groups' => ['read']], denormalizationContext: ['groups' => ['write']])]
#[ORM\Entity]
#[ORM\Table(name: "user_group")]
class Group
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
	#[Groups("read")]
    private $id;

    #[ORM\Column(length: 255, type: 'string')]
	#[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(nullable: true, type: 'text')]
	#[Groups(['read', 'write'])]
    private $description;

    #[ORM\ManyToMany(targetEntity:"App\Entity\User", inversedBy:"groups")]
	#[Groups(['read', 'write'])]
    private $users;

	#[ORM\Column(type: "string", length: 50, nullable: true)]
	#[Groups(['read', 'write'])]
    private $role;
    
    #[ORM\OneToMany(targetEntity:"App\Entity\Project", mappedBy:"group")]
	#[ORM\JoinColumn(nullable:true)]
	#[Groups(['read', 'write'])]
    private $projects;

	// add permissions array property to Group entity
	#[ORM\Column(type: "array", nullable: true)]
	#[Groups(['read', 'write'])]
	private $permissions = [];	

    // Other properties, getters, and setters

	public function __construct()
    {
        $this->users = new ArrayCollection();
    }

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param mixed $id 
	 * @return self
	 */
	public function setId($id): self {
		$this->id = $id;
		return $this;
	}

	public function getProjects(): Collection
	{
		return $this->projects;
	}

	public function addProject(Project $project): self
	{
		if (!$this->projects->contains($project)) {
			$this->projects[] = $project;
			$project->setGroup($this);
		}

		return $this;
	}

	public function removeProject(Project $project): self
	{
		if ($this->projects->removeElement($project)) {
			// set the owning side to null (unless already changed)
			if ($project->getGroup() === $this) {
				$project->setGroup(null);
			}
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
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

	/**
	 * @return mixed
	 */
	public function getUsers() {
		return $this->users;
	}
	
	/**
	 * @param mixed $users 
	 * @return self
	 */
	public function setUsers($users): self {
		$this->users = $users;
		return $this;
	}

	public function addUser(User $user): self
	{
		if (!$this->users->contains($user)) {
			$this->users[] = $user;
			$user->addGroup($this);
		}

		return $this;
	}

	public function removeUser(User $user): self
	{
		if ($this->users->removeElement($user)) {
			
		}

		return $this;
	}

	// toStringd
	public function __toString(): string
	{
		return $this->name;
	}

	    /**
     * Get the role associated with the group.
     *
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * Set the role associated with the group.
     *
     * @param string $role
     * @return self
     */
    public function setRole(string $role): self
    {
        // Optionally, you could validate the role here, e.g.:
        // if (!in_array($role, [self::ROLE_ADMIN, self::ROLE_LAB_MANAGER, self::ROLE_RESEARCHER, self::ROLE_TECHNICIAN])) {
        //     throw new \InvalidArgumentException('Invalid role');
        // }

        $this->role = $role;
        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getPermissions() {
		return $this->permissions;
	}
	
	/**
	 * @param mixed $permissions 
	 * @return self
	 */
	public function setPermissions($permissions): self {
		$this->permissions = $permissions;
		return $this;
	}
}
