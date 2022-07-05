<?php

namespace App\Repository;

use App\Entity\TypeRdv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeRdv|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeRdv|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeRdv[]    findAll()
 * @method TypeRdv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeRdvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeRdv::class);
    }

    // /**
    //  * @return TypeRdv[] Returns an array of TypeRdv objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeRdv
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
