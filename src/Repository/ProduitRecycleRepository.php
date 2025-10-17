<?php

namespace App\Repository;

use App\Entity\ProduitRecycle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProduitRecycle>
 *
 * @method ProduitRecycle|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitRecycle|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitRecycle[]    findAll()
 * @method ProduitRecycle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRecycleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitRecycle::class);
    }

//    /**
//     * @return ProduitRecycle[] Returns an array of ProduitRecycle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProduitRecycle
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
