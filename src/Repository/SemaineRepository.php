<?php

namespace App\Repository;

use App\Entity\Semaine;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
     * @throws NonUniqueResultException
     */
    public function findAtSameDate($date): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere("DATE_FORMAT(s.dateDebut, '%d/%m/%Y') = :date")
            ->setParameter('date', $date)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Semaine[]
     */
    public function findAllDates(): array
    {
        return $this->createQueryBuilder('s')
            ->select("DATE_FORMAT(s.dateDebut, '%d/%m/%Y') as date")
            ->orderBy('s.dateDebut', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Semaine[]
     */
    public function findByYear(int $year): array
    {
        $from = new DateTimeImmutable("$year-01-01");
        $to = new DateTimeImmutable("$year-12-31 23:59:59");

        return $this->createQueryBuilder('s')
            ->where('s.dateDebut BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('s.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
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
