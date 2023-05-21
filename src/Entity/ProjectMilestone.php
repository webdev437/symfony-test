<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Faker\Provider\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectMilestoneRepository")
 */
class ProjectMilestone implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectMilestones",cascade={"persist"})
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     */
    private $project;

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
     * @ORM\Column(type="datetime")
     * @var \DateTimeImmutable|null
     */
    private $milestoneDeadline;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $deleted;

    /**
     * @param Project $project
     * @throws Exception
     */
    public function __construct(Project $project)
    {
        $this->id = Uuid::uuid();
        $this->deleted = false;

        $this->project = $project;
        $this->milestoneDeadline = new \DateTimeImmutable();
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
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }
 
    /**
     * @param Project $project
     * @return void
     */
    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getMilestoneDeadline(): ?\DateTimeImmutable
    {
        return $this->milestoneDeadline;
    }
 
    /**
     * @param \DateTimeImmutable|null $milestoneDeadline
     * @return void
     */
    public function setMilestoneDeadline(?\DateTimeImmutable $milestoneDeadline): void
    {
        $this->milestoneDeadline = $milestoneDeadline;
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
            "project" => $stopRecursion ? $this->project->getId() : $this->project->jsonSerialize(true),
            "title" => $this->getTitle(),
            "description" => $this->getDescription(),
            "milestoneDeadline" => $this->milestoneDeadline ? $this->milestoneDeadline->format('c') : null
        ];
    }
}
