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
            ->getQuery()
            ->getResult();
    }

    
    public function findAllDates($categorie)
    {
        $qb = $this->createQueryBuilder('s');

        $qb->andWhere('s.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->select("DATE_FORMAT(s.date, '%d/%m/%Y') as date, SIZE(s.rendezVous) as HIDDEN rendezVous")
            ->orderBy('s.date', 'ASC')
            ->groupBy('s.id')
            ->having('rendezVous < 1')
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
            ->select("SIZE(s.rendezVous) as HIDDEN rendezVous, DATE_FORMAT(s.heureDebut, '%H:%i') as hour, s.id")
            ->orderBy('s.heureDebut', 'ASC')
            ->groupBy('s.id')
            ->having('rendezVous < 1')
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
