<?php

namespace App\Repository;

use App\Entity\Atelier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Atelier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Atelier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Atelier[]    findAll()
 * @method Atelier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AtelierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Atelier::class);
    }

    public function findClosestRdv(\DateTime $date, $id)
    {
        $qb = $this
            ->createQueryBuilder('a')

            ->andWhere('a.date >= :date')
            ->setParameter('date', $date)
            ->orderBy('a.date', 'ASC')

            ->leftJoin('a.patient', 'p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)

            ->setMaxResults(1)
            ;
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneByDateJoinedToAtelier(\DateTime $date1, \DateTime $date2)
    {
        return $this->createQueryBuilder('e')
            ->where('e.date >= :date1')
            ->andWhere('e.date <= :date2')
            ->setParameter('date1', $date1)
            ->setParameter('date2', $date2)
            ->getQuery()
            ->getResult();
    }

    public function findSameDate($year, $month, $day, $id)
    {
        return $this->createQueryBuilder('e')
            ->where('YEAR(e.date) = :year')
            ->andWhere('MONTH(e.date) = :month')
            ->andWhere('DAY(e.date) = :day')
            ->leftJoin('e.patient', 'p')
            ->andWhere('p.id = :id')
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->setParameter('day', $day)
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Atelier[] Returns an array of Atelier objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Atelier
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
