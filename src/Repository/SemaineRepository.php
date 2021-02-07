<?php

namespace App\Repository;

use App\Entity\Semaine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Semaine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Semaine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Semaine[]    findAll()
 * @method Semaine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SemaineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Semaine::class);
    }

    /**
    * @return Semaine[] Returns true if same dateDebut
    */

    public function findSemaineAtSameDate($date)
    {
        return $this->createQueryBuilder('s')
            ->andWhere("DATE_FORMAT(s.dateDebut, '%d/%m/%Y') = :date")
            ->setParameter('date', $date)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // /**
    //  * @return Semaine[] Returns an array of Semaine objects
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
    public function findOneBySomeField($value): ?Semaine
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
