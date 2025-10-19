<?php

namespace App\Repository;

use App\Entity\Dechet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DechetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dechet::class);
    }

    public function getWorkflowStats(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN etat = 'declare' THEN 1 ELSE 0 END) as declares,
                SUM(CASE WHEN etat = 'en_processus' THEN 1 ELSE 0 END) as en_processus,
                SUM(CASE WHEN etat = 'transforme' THEN 1 ELSE 0 END) as transformes,
                AVG(quantite) as moyenne_quantite,
                MAX(date_production) as dernier_ajout
            FROM dechet
        ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAssociative();
    }

    public function getMonthlyStats(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT 
                DATE_FORMAT(date_production, '%Y-%m') as mois,
                COUNT(*) as total,
                SUM(quantite) as quantite_totale
            FROM dechet 
            WHERE date_production >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(date_production, '%Y-%m')
            ORDER BY mois
        ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    public function getStatsByType(): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.type, COUNT(d.id) as count, SUM(d.quantite) as quantite')
            ->groupBy('d.type')
            ->getQuery()
            ->getResult();
    }

    public function getTransformationEfficiency(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT 
            COUNT(*) as total_dechets,
            SUM(CASE WHEN d.produitrecycle_id IS NOT NULL THEN 1 ELSE 0 END) as transformes,
            ROUND((SUM(CASE WHEN d.produitrecycle_id IS NOT NULL THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as taux_transformation
        FROM dechet d
    ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAssociative();
    }
    public function getWeeklyStats(): array
{
    $conn = $this->getEntityManager()->getConnection();

    $sql = "
        SELECT 
            YEARWEEK(date_production) as semaine,
            COUNT(*) as total,
            AVG(DATEDIFF(NOW(), date_production)) as temps_moyen
        FROM dechet 
        WHERE date_production >= DATE_SUB(NOW(), INTERVAL 8 WEEK)
        GROUP BY YEARWEEK(date_production)
        ORDER BY semaine DESC
        LIMIT 6
    ";

    $stmt = $conn->prepare($sql);
    $result = $stmt->executeQuery();

    return $result->fetchAllAssociative();
}
}