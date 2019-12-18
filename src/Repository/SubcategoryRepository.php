<?php

namespace App\Repository;

use App\Entity\Subcategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Subcategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subcategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subcategory[]    findAll()
 * @method Subcategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubcategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subcategory::class);
    }

    /**
     * @return Subcategory[] Returns an array of Subcategory objects
     */

    public function findByCategory($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.category = :val')
            ->setParameter('val', $value)
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByPosition($value, $cat): ?Subcategory
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.position = :val')
            ->andWhere('c.category = :cat')
            ->setParameter('val', $value)
            ->setParameter('cat', $cat)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
