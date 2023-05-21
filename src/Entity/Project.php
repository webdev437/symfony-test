<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Faker\Provider\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="projects",cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $deleted;

    /**
     * @ORM\OneToMany(targetEntity="ProjectMilestone", mappedBy="project")
     * @var ArrayCollection
     */
    private $projectMilestones;

    /**
     * @param User $user
     * @throws Exception
     */
    public function __construct(User $user)
    {
        $this->id = Uuid::uuid();
        $this->deleted = false;

        $this->user = $user;
        $this->projectMilestones = new ArrayCollection();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function delete() : void
    {
        $this->deleted = true;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return void
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
 
    /**
     * @param User $user
     * @return void
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Collection|ProjectMilestone[]
     */
    public function getProjectMilestones(): Collection
    {
        return $this->projectMilestones;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(bool $stopRecursion = false)
    {
        return [
            "id" => $this->getId(),
            "user" => $stopRecursion ? $this->user->getId() : $this->user->jsonSerialize(true),
            "title" => $this->getTitle(),
            "description" => $this->getDescription()
        ];
    }

}
