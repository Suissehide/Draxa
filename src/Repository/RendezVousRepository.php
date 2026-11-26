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

    /**
     * Prochain rendez-vous (à partir de $from) de chaque patient, en une seule requête.
     *
     * @param int[] $patientIds
     * @return array<int, RendezVous> indexé par id de patient
     */
    public function findNextByPatients(array $patientIds, \DateTimeInterface $from): array
    {
        if (!$patientIds) {
            return [];
        }

        $rdvs = $this->createQueryBuilder('r')
            ->addSelect('s')
            ->leftJoin('r.slot', 's')
            ->andWhere('IDENTITY(r.patient) IN (:ids)')
            ->andWhere('r.date >= :from')
            ->setParameter('ids', $patientIds)
            ->setParameter('from', $from)
            ->orderBy('r.date', 'ASC')
            ->addOrderBy('r.heure', 'ASC')
            ->addOrderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult();

        $next = [];
        foreach ($rdvs as $rdv) {
            $next[$rdv->getPatient()->getId()] ??= $rdv;
        }

        return $next;
    }

    /**
     * @return RendezVous[]
     */
    public function findSameDate(?string $date, $patientId, ?string $categorie): array
    {
        return $this->createQueryBuilder('e')
            ->where("DATE_FORMAT(e.date, '%d/%m/%Y') = :date")
            ->andWhere('e.categorie = :categorie')
            ->andWhere('IDENTITY(e.patient) = :id')
            ->setParameter('date', $date)
            ->setParameter('categorie', $categorie)
            ->setParameter('id', $patientId)
            ->getQuery()
            ->getResult();
    }
}
