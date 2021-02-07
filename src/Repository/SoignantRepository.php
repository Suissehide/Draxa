<?php

namespace App\Repository;

use App\Entity\Soignant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Soignant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Soignant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Soignant[]    findAll()
 * @method Soignant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SoignantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Soignant::class);
    }

    // /**
    //  * @return Soignant[] Returns an array of Soignant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Soignant
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
