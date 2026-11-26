<?php

namespace App\Repository;

use App\Entity\Slot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Slot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slot[]    findAll()
 * @method Slot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotRepository extends ServiceEntityRepository
{
    /** Colonnes triables depuis la grille (clé reçue => champ DQL). */
    private const SORTABLE = [
        'date' => 's.date',
        'categorie' => 's.categorie',
        'thematique' => 's.thematique',
        'type' => 's.type',
        'location' => 's.location',
        'place' => 's.place',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    /**
     * @return Slot[]
     */
    public function findByDate(\DateTimeInterface $date1, \DateTimeInterface $date2): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.date >= :date1')
            ->andWhere('s.date <= :date2')
            ->setParameter('date1', $date1)
            ->setParameter('date2', $date2)
            ->orderBy('s.heureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllDates(string $categorie): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->select("DATE_FORMAT(s.date, '%d/%m/%Y') as date, SIZE(s.rendezVous) as rendezVous, s.place as place")
            ->groupBy('s.id')
            ->having('rendezVous < place')
            ->orderBy('s.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getHoursOfDate($categorie, $date): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->andWhere("DATE_FORMAT(s.date, '%d/%m/%Y') = :date")
            ->setParameter('date', $date)
            ->select("SIZE(s.rendezVous) as HIDDEN rendezVous, DATE_FORMAT(s.heureDebut, '%H:%i') as hour, s.id, s.place as HIDDEN place")
            ->leftJoin('s.soignant', 'soignant')
            ->addOrderBy('s.heureDebut', 'ASC')
            ->addOrderBy('soignant.priority', 'ASC')
            ->groupBy('s.id')
            ->having('rendezVous < place')
            ->getQuery()
            ->getResult();
    }

    public function getThematiqueTypeOfId($id): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->select('s.thematique, s.type')
            ->getQuery()
            ->getResult();
    }

    public function findByFilter(?array $sort, string $searchPhrase, ?array $categories, ?string $dateDebut, ?string $dateFin): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s');

        if ($searchPhrase !== '') {
            $qb
                ->leftJoin('s.soignant', 't')
                ->andWhere("
                    DATE_FORMAT(s.date, '%d/%m/%Y') LIKE :search
                    OR s.heureDebut LIKE :search
                    OR s.heureFin LIKE :search
                    OR s.thematique LIKE :search
                    OR s.type LIKE :search
                    OR s.location LIKE :search
                    OR t.nom LIKE :search
                    OR t.prenom LIKE :search
                ")
                ->setParameter('search', '%' . $searchPhrase . '%');
        }
        if ($dateDebut && $from = \DateTime::createFromFormat('!d/m/Y', $dateDebut)) {
            $qb->andWhere('s.date >= :dateDebut')->setParameter('dateDebut', $from);
        }
        if ($dateFin && $to = \DateTime::createFromFormat('!d/m/Y', $dateFin)) {
            $qb->andWhere('s.date <= :dateFin')->setParameter('dateFin', $to);
        }
        if ($categories) {
            $qb->andWhere('s.categorie IN (:categories)')->setParameter('categories', array_values($categories));
        }

        $sorted = false;
        foreach ($sort ?? [] as $key => $dir) {
            $dir = strtoupper((string) $dir) === 'DESC' ? 'DESC' : 'ASC';
            if ($key === 'horaire') {
                $qb->addOrderBy('s.date', 'ASC')->addOrderBy('TIME(s.heureDebut)', $dir);
                $sorted = true;
            } elseif (isset(self::SORTABLE[$key])) {
                $qb->addOrderBy(self::SORTABLE[$key], $dir);
                $sorted = true;
            }
        }
        if (!$sorted) {
            $qb->addOrderBy('s.date', 'ASC')->addOrderBy('TIME(s.heureDebut)', 'ASC');
        }

        return $qb;
    }

    public function countFiltered(QueryBuilder $qb): int
    {
        return (int) (clone $qb)
            ->select('COUNT(DISTINCT s.id)')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function isAlreadyInSlot($slotId, $patientId): bool
    {
        return (bool) $this->createQueryBuilder('s')
            ->select('COUNT(r.id)')
            ->innerJoin('s.rendezVous', 'r')
            ->andWhere('s.id = :slotId')
            ->andWhere('IDENTITY(r.patient) = :patientId')
            ->setParameter('slotId', $slotId)
            ->setParameter('patientId', $patientId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
