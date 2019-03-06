<?php

namespace App\Repository;

use App\Entity\BCVs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BCVs|null find($id, $lockMode = null, $lockVersion = null)
 * @method BCVs|null findOneBy(array $criteria, array $orderBy = null)
 * @method BCVs[]    findAll()
 * @method BCVs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BCVsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BCVs::class);
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

    public function findOneByDateJoinedToBCVs(\DateTime $date1, \DateTime $date2)
    {
        return $this->createQueryBuilder('e')
            ->where('e.date >= :date1')
            ->andWhere('e.date <= :date2')
            ->setParameter('date1', $date1)
            ->setParameter('date2', $date2)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return BCVs[] Returns an array of BCVs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BCVs
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
