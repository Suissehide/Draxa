<?php

namespace App\Repository;

use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;


/**
 * @method Patient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Patient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Patient[]    findAll()
 * @method Patient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Patient::class);
	}
    
    public function compte()
    {
        return $this->createQueryBuilder('p')
                    ->select('COUNT(p)')
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function findByFilter($sort, $searchPhrase, $etat)
    {
        $qb = $this->createQueryBuilder('p');

        if ($searchPhrase != "") {
            $qb->andWhere('p.nom LIKE :search
                OR p.prenom LIKE :search
                OR p.telephone LIKE :search
                OR p.etp LIKE :search
            ')
                ->setParameter('search', '%' . $searchPhrase . '%');
        }
        if ($etat == "in") {
            $qb->andWhere('p.dentree IS NULL');
        }
        else if ($etat == "out") {
            $qb->andWhere('p.dentree LIKE :val')
            ->setParameter('val', '%');
        }
        if ($sort) {
            foreach ($sort as $key => $value) {
                $qb->orderBy('p.' . $key, $value);
            }
        } else {
            $qb->orderBy('p.dedate', 'DESC');
        }
        return $qb;
    }

//    /**
//     * @return Patient[] Returns an array of Patient objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Patient
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
