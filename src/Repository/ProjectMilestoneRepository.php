<?php

namespace App\Repository;

use App\Entity\ProjectMilestone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProjectMilestone|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectMilestone|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectMilestone[]    findAll()
 * @method ProjectMilestone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectMilestoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectMilestone::class);
    }

    /**
     * @param int $value
     * @return ProjectMilestone[] Returns an array of ProjectMilestone objects
     */
    public function findByProject($value)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin("p.project", "project")
            ->andWhere('project.id = :projectId')
            ->setParameter('projectId', $value)
            ->getQuery()
            ->getResult()
        ;
    }
}