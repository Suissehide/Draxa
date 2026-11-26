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

    /**
     * Plus grande priorité attribuée (0 s'il n'y a aucun soignant).
     */
    public function getPriorityMax(): int
    {
        return (int) $this->createQueryBuilder('s')
            ->select('MAX(s.priority)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
