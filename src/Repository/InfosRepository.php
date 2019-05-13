<?php

namespace App\Repository;

use App\Entity\Infos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Infos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Infos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Infos[]    findAll()
 * @method Infos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfosRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Infos::class);
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

    public function findOneByDateJoinedToInfos(\DateTime $date1, \DateTime $date2)
    {
        return $this->createQueryBuilder('e')
            ->where('e.date >= :date1')
            ->andWhere('e.date <= :date2')
            ->setParameter('date1', $date1)
            ->setParameter('date2', $date2)
            ->getQuery()
            ->getResult();
    }

    public function findSameDate($year, $month, $day)
    {
        return $this->createQueryBuilder('e')
            ->where('YEAR(e.date) = :year')
            ->andWhere('MONTH(e.date) = :month')
            ->andWhere('DAY(e.date) = :day')
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->setParameter('day', $day)
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Infos[] Returns an array of Infos objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Infos
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
