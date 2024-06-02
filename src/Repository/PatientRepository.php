<?php

namespace App\Repository;

use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Patient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Patient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Patient[]    findAll()
 * @method Patient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
	}

    public function countPatient()
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findAndCountByFilter($sort, $searchPhrase, $etat, $rowCount, $current)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.rendezVous', 'r')
            ->leftJoin('r.slot', 's')
            ->select('DISTINCT p'); // Ensure unique patients

        if (!empty($searchPhrase)) {
            $qb->andWhere("
            p.nom LIKE :search
            OR p.prenom LIKE :search
            OR p.tel1 LIKE :search
            OR p.etp LIKE :search
            OR p.objectif LIKE :search
            OR r.categorie LIKE :search
            OR DATE_FORMAT(r.date, '%d/%m/%Y') LIKE :search
            OR s.thematique LIKE :search
        ")
                ->setParameter('search', '%' . $searchPhrase . '%');
        }

        if ($etat === "in") {
            $qb->andWhere('p.dentree IS NULL');
        } elseif ($etat === "out") {
            $qb->andWhere('p.dentree IS NOT NULL');
        } elseif ($etat !== "all") {
            $qb->andWhere('p.offre LIKE :etat')
                ->setParameter('etat', $etat);
        }

        if ($sort) {
            foreach ($sort as $key => $value) {
                if ($key == "date" || $key == "categorie") {
                    $qb->orderBy('r.' . $key, $value);
                } elseif ($key == "thematique") {
                    $qb->orderBy('s.' . $key, $value);
                } else {
                    $qb->orderBy('p.' . $key, $value);
                }
            }
        } else {
            $qb->orderBy('p.nom', 'ASC');
        }

        // Clone the query builder to create the count query
        $countQb = clone $qb;
        $countQb->select('COUNT(DISTINCT p.id)'); // Count unique patients

        // Get the count result
        $count = $countQb->getQuery()->getSingleScalarResult();

        // Apply pagination
        if ($rowCount != -1) {
            $min = ($current - 1) * $rowCount;
            $qb->setFirstResult($min)->setMaxResults($rowCount);
        }

        // Get the results
        $patients = $qb->getQuery();

        return ['count' => $count, 'patients' => $patients];
    }
}
