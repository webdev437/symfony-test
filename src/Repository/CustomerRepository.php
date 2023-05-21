<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class, Orders::class);
    }

    /**
    * @return Customer|null  
    */
    public function findByCustomerId(string $customerId)
    {
        return $this->findOneBy([
            'customerId' => $customerId
        ]);
    }

    /**
     * @return Customer[] Returns an array of Customers objects
     */
    public function findUndeletedCustomers()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isDeleted is NULL')
            ->getQuery()
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Customers
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
