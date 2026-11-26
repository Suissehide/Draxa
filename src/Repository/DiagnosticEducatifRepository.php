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
}
