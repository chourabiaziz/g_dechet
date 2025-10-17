<?php

namespace App\Repository;

use App\Entity\BoucleEconomieCirculaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BoucleEconomieCirculaire>
 *
 * @method BoucleEconomieCirculaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoucleEconomieCirculaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoucleEconomieCirculaire[]    findAll()
 * @method BoucleEconomieCirculaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoucleEconomieCirculaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoucleEconomieCirculaire::class);
    }

//    /**
//     * @return BoucleEconomieCirculaire[] Returns an array of BoucleEconomieCirculaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BoucleEconomieCirculaire
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
