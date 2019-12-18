<?php

namespace App\Repository;

use App\Entity\CustomerOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CustomerOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerOrder[]    findAll()
 * @method CustomerOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerOrder::class);
    }

    /**
     * @return CustomerOrder[] Returns an array of CustomerOrder objects
     */
    public function findByUser($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :val')
            ->setParameter('val', $value)
            ->orderBy('c.order_date', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?CustomerOrder
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
