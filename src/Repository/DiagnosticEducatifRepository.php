<?php

namespace App\Repository;

use App\Entity\DiagnosticEducatif;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DiagnosticEducatif|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiagnosticEducatif|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiagnosticEducatif[]    findAll()
 * @method DiagnosticEducatif[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiagnosticEducatifRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiagnosticEducatif::class);
    }

    // /**
    //  * @return DiagnosticEducatif[] Returns an array of DiagnosticEducatif objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DiagnosticEducatif
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
