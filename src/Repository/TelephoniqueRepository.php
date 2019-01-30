<?php

namespace App\Repository;

use App\Entity\Telephonique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Telephonique|null find($id, $lockMode = null, $lockVersion = null)
 * @method Telephonique|null findOneBy(array $criteria, array $orderBy = null)
 * @method Telephonique[]    findAll()
 * @method Telephonique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TelephoniqueRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Telephonique::class);
    }

//    /**
//     * @return Telephonique[] Returns an array of Telephonique objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Telephonique
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
