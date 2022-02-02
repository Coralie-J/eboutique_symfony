<?php

namespace App\Repository;

use App\Entity\PanierLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PanierLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method PanierLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method PanierLine[]    findAll()
 * @method PanierLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PanierLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PanierLine::class);
    }

    // /**
    //  * @return PanierLine[] Returns an array of PanierLine objects
    //  */
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
    public function findOneBySomeField($value): ?PanierLine
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
