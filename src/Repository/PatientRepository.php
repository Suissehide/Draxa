<?php

namespace App\Repository;

use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Patient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Patient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Patient[]    findAll()
 * @method Patient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientRepository extends ServiceEntityRepository
{
    /** Colonnes de Patient triables en base. */
    public const SORTABLE = ['id', 'nom', 'prenom', 'tel1', 'etp', 'objectif'];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }

    /**
     * Patients filtrés pour la grille de la liste des patients.
     * Le tri n'est appliqué que pour les colonnes de SORTABLE ; les autres sont triées par l'appelant.
     */
    public function createFilterQueryBuilder(?string $searchPhrase, ?string $etat, ?string $sortKey, string $sortDir): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        if ($searchPhrase) {
            // Sous-requête : évite le DISTINCT + jointure qui casse le tri et la pagination.
            $rdvMatch = $this->getEntityManager()->createQueryBuilder()
                ->select('1')
                ->from('App\Entity\RendezVous', 'r')
                ->leftJoin('r.slot', 's')
                ->where('r.patient = p')
                ->andWhere("r.categorie LIKE :search OR DATE_FORMAT(r.date, '%d/%m/%Y') LIKE :search OR s.thematique LIKE :search");

            $qb->andWhere(
                'p.nom LIKE :search OR p.prenom LIKE :search OR p.tel1 LIKE :search'
                . ' OR p.etp LIKE :search OR p.objectif LIKE :search'
                . ' OR EXISTS(' . $rdvMatch->getDQL() . ')'
            )->setParameter('search', '%' . $searchPhrase . '%');
        }

        if ($etat === 'in') {
            $qb->andWhere('p.dentree IS NULL');
        } elseif ($etat === 'out') {
            $qb->andWhere('p.dentree IS NOT NULL');
        }

        if (in_array($sortKey, self::SORTABLE, true)) {
            $qb->orderBy('p.' . $sortKey, $sortDir === 'DESC' ? 'DESC' : 'ASC');
        } else {
            $qb->orderBy('p.nom', 'ASC');
        }

        return $qb;
    }

    public function countFiltered(QueryBuilder $qb): int
    {
        return (int) (clone $qb)
            ->select('COUNT(p.id)')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
