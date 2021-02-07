<?php

namespace App\Repository;

use App\Entity\RendezVous;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RendezVous|null find($id, $lockMode = null, $lockVersion = null)
 * @method RendezVous|null findOneBy(array $criteria, array $orderBy = null)
 * @method RendezVous[]    findAll()
 * @method RendezVous[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
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

    public function findOneByDateJoinedToRendezVous(\DateTime $date1, \DateTime $date2, $categorie)
    {
        return $this->createQueryBuilder('e')
            ->where('e.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->andWhere('e.date >= :date1')
            ->andWhere('e.date <= :date2')
            ->setParameter('date1', $date1)
            ->setParameter('date2', $date2)
            ->getQuery()
            ->getResult();
    }

    public function findSameDate($date, $id, $categorie)
    {
        return $this->createQueryBuilder('e')
            ->where("DATE_FORMAT(e.date, '%d/%m/%Y') = :date")
            ->setParameter('date', $date)

            ->andWhere('e.categorie = :categorie')
            ->setParameter('categorie', $categorie)

            ->leftJoin('e.patient', 'p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)

            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return RendezVous[] Returns an array of RendezVous objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RendezVous
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
