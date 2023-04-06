<?php

namespace App\Repository;

use App\Entity\Slot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Slot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slot[]    findAll()
 * @method Slot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }
    
    /**
     * @return Slot[] Returns an array of Patient's name
     */
    public function findAllPatientWithSearch(?string $term)
    {
        $qb = $this->createQueryBuilder('s')
                    ->leftJoin('s.rendezVous', 'r')
                    ->leftJoin('r.patient', 'p');
        if ($term) {
            $qb->andWhere('p.nom LIKE :term OR p.prenom LIKE :term')
                ->setParameter('term', '%' . $term . '%')
            ;
        }
        return $qb
            ->orderBy('p.nom', 'DESC')
            ->select('p.name AND p.prenom AND p.id')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByDate(\DateTime $date1, \DateTime $date2)
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

    
    public function findAllDates($categorie)
    {
        $qb = $this->createQueryBuilder('s');

        $qb->andWhere('s.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->select("DATE_FORMAT(s.date, '%d/%m/%Y') as date, SIZE(s.rendezVous) as rendezVous, s.place as place")
            ->orderBy('s.date', 'ASC')
            ->groupBy('s.id')
            ->having('rendezVous < place')
            ->distinct()
        ;

        return $qb->getQuery()->getResult();
    }

    public function getHoursOfDate($categorie, $date)
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
    
    public function getThematiqueTypeOfId($id)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->select("s.thematique, s.type")
            ->getQuery()
            ->getResult();
    } 

    public function findByFilter($sort, $searchPhrase, $categories, $dateDebut, $dateFin)
    {
        $qb = $this->createQueryBuilder('s');

        if ($searchPhrase != "") {
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
        if ($dateDebut) {
            $qb->andWhere("s.date >= :dateDebut")
            ->setParameter('dateDebut', \DateTime::createFromFormat("d/m/Y H:i:s", date($dateDebut . " 00:00:00")));
        }
        if ($dateFin) {
            $qb->andWhere("s.date <= :dateFin")
            ->setParameter('dateFin', \DateTime::createFromFormat("d/m/Y H:i:s", date($dateFin . " 23:59:59")));
        }
        if ($categories) {
            $query = '';
            foreach ($categories as $key => $value) {
                if ($key === count($categories) - 1)
                    $query .= 's.categorie = :' . $value;
                else
                    $query .= 's.categorie = :' . $value . ' OR ';
                $qb->setParameter($value, $value);
            }
            $qb->andWhere($query);
        }
        if ($sort) {
            foreach ($sort as $key => $value) {
                if ($key !== 'horaire')
                    $qb->addOrderBy('s.' . $key, $value);
                else {
                    $qb
                        ->addOrderBy('s.date', 'ASC')
                        ->addOrderBy('TIME(s.heureDebut)', $value)
                    ;
                }
            }
        } else {
            $qb
            ->addOrderBy('s.date', 'ASC')
            ->addOrderBy('TIME(s.heureDebut)', 'ASC')
            ;
        }
        return $qb;
    }

    public function isAlreadyInSlot($slotId, $patientId)
    {
        return $this->createQueryBuilder('s')
            ->andWhere("s.id = :slotId")
            ->setParameter('slotId', $slotId)

            ->leftJoin('s.rendezVous', 'r')
            ->leftJoin('r.patient', 'p')
            ->andWhere("p.id = :patientId")
            ->setParameter('patientId', $patientId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findSlotEducative($dateStart, $dateEnd)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->andWhere('s.date BETWEEN :dateStart AND :dateEnd')
            ->setParameter('dateStart', $dateStart->format('Y-m-d'))
            ->setParameter('dateEnd', $dateEnd->format('Y-m-d'))
            ->andWhere('s.categorie = :categorie')
            ->setParameter('categorie', "Educative")
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findSlotAtelier($dateStart, $dateEnd)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->andWhere('s.date BETWEEN :dateStart AND :dateEnd')
            ->setParameter('dateStart', $dateStart->format('Y-m-d'))
            ->setParameter('dateEnd', $dateEnd->format('Y-m-d'))
            ->andWhere('s.categorie = :categorie')
            ->setParameter('categorie', "Atelier")
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    // /**
    //  * @return Slot[] Returns an array of Slot objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Slot
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
