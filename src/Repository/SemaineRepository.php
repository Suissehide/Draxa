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
     * Semaine commençant à la date donnée (format d/m/Y), ou null.
     */
    public function findAtSameDate(?string $date): ?Semaine
    {
        return $this->createQueryBuilder('s')
            ->andWhere("DATE_FORMAT(s.dateDebut, '%d/%m/%Y') = :date")
            ->setParameter('date', $date)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllDates(): array
    {
        return $this->createQueryBuilder('s')
            ->select("DATE_FORMAT(s.dateDebut, '%d/%m/%Y') as date")
            ->orderBy('s.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Semaine[]
     */
    public function findByYear(int $year): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.dateDebut BETWEEN :from AND :to')
            ->setParameter('from', new \DateTimeImmutable("$year-01-01"))
            ->setParameter('to', new \DateTimeImmutable("$year-12-31 23:59:59"))
            ->orderBy('s.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
