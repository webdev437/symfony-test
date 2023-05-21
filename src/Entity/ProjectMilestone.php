<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectMilestoneRepository")
 */
class ProjectMilestone
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
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
     * @var \DateTime|null
     */
    private $milestoneDeadline;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $isDeleted;

    /**
     * @return string|null
     */
    public function getIsDeleted(): ?string
    {
        return $this->isDeleted;
    }

    /**
     * @param string|null $isDeleted
     * @return User
     */
    public function setIsDeleted(?string $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): ?int
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
     * @return \DateTime|null
     */
    public function getMilestoneDeadline(): ?\DateTime
    {
        return $this->milestoneDeadline;
    }

    /**
     * @param \DateTime|null $milestoneDeadline
     * @return void
     */
    public function setMilestoneDeadline(?\DateTime $milestoneDeadline): void
    {
        $this->milestoneDeadline = $milestoneDeadline;
    }
}