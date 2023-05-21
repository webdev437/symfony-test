<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * @param int $value
     * @return Project[] Returns an array of Project objects
     */
    public function findByUser($value)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin("p.user", "user")
            ->andWhere('user.id = :userId')
            ->setParameter('userId', $value)
            ->getQuery()
            ->getResult()
        ;
    }
}