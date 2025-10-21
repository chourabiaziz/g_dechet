<?php
// src/Repository/ActeurRepository.php
namespace App\Repository;

use App\Entity\Acteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ActeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Acteur::class);
    }

 public function searchPaginated(?string $q, string $sort, string $dir, int $page, int $limit): array
{
    $allowedSort = [
        'id'         => 'a.id',
        'nom'        => 'a.nom',
        'role'       => 'a.role',
        'nbDechets'  => 'nbDechets', // alias defined below
    ];
    $dir   = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';
    $page  = max(1, $page);
    $limit = min(max(5, $limit), 100);

    // ---- total distinct acteurs (with same filters) ----
    $qbCount = $this->createQueryBuilder('a')
        ->select('COUNT(DISTINCT a.id)')
        ->leftJoin('a.dechets', 'd');
    if ($q) {
        $qbCount->andWhere('a.nom LIKE :q OR a.role LIKE :q')->setParameter('q', '%'.$q.'%');
    }
    $total = (int)$qbCount->getQuery()->getSingleScalarResult();

    // ---- page rows (hydrate as ARRAY so alias nbDechets is present) ----
    $qb = $this->createQueryBuilder('a')
        ->select('a.id AS id, a.nom AS nom, a.role AS role, COUNT(d.id) AS nbDechets')
        ->leftJoin('a.dechets', 'd')
        ->groupBy('a.id');

    if ($q) {
        $qb->andWhere('a.nom LIKE :q OR a.role LIKE :q')->setParameter('q', '%'.$q.'%');
    }

    $sortBy = $allowedSort[$sort] ?? 'a.id';
    $qb->orderBy($sortBy, $dir)
       ->setFirstResult(($page - 1) * $limit)
       ->setMaxResults($limit);

    $rows = $qb->getQuery()->getArrayResult(); // <— key point

    $items = array_map(static function(array $r): array {
        return [
            'id'        => (int)$r['id'],
            'nom'       => (string)$r['nom'],
            'role'      => (string)$r['role'],
            'nbDechets' => (int)$r['nbDechets'], // no more "undefined"
        ];
    }, $rows);

    return ['items' => $items, 'total' => $total];
}

 



 // src/Repository/ActeurRepository.php
public function getStats(?string $q = null): array
{
    // Répartition par rôle
    $qb1 = $this->createQueryBuilder('a')
        ->select('a.role AS role, COUNT(a.id) AS total')
        ->groupBy('a.role')
        ->orderBy('total', 'DESC');
    if ($q) {
        $qb1->andWhere('a.nom LIKE :q OR a.role LIKE :q')
            ->setParameter('q', '%'.$q.'%');
    }
    $roles = $qb1->getQuery()->getArrayResult();

    // Top 5 par nombre de déchets (DESC)
    $qbTop = $this->createQueryBuilder('a')
        ->leftJoin('a.dechets', 'd')
        ->addSelect('a.nom AS nom')
        ->addSelect('COUNT(d.id) AS nbDechets')
        ->groupBy('a.id')
        ->orderBy('nbDechets', 'DESC')
        ->addOrderBy('a.nom', 'ASC')
        ->setMaxResults(5);
    if ($q) {
        $qbTop->andWhere('a.nom LIKE :q OR a.role LIKE :q')
              ->setParameter('q', '%'.$q.'%');
    }
    $topByDechets = array_map(fn($r) => [
        'nom' => (string)$r['nom'],
        'nbDechets' => (int)$r['nbDechets'],
    ], $qbTop->getQuery()->getArrayResult());

    return [
        'roles' => $roles,           // [{role,total}]
        'topByDechets' => $topByDechets, // [{nom,nbDechets}]
    ];
}


    /** Pour export CSV/XLSX avec filtres/sort identiques */
    public function searchAllForExport(?string $q, string $sort, string $dir): array
    {
        $allowedSort = ['id' => 'a.id', 'nom' => 'a.nom', 'role' => 'a.role'];
        $sortBy = $allowedSort[$sort] ?? 'a.id';
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

        $qb = $this->createQueryBuilder('a');
        if ($q) {
            $qb->andWhere('a.nom LIKE :q OR a.role LIKE :q')->setParameter('q', '%'.$q.'%');
        }
        $qb->orderBy($sortBy, $dir);

        return array_map(function(Acteur $a) {
            return ['id'=>$a->getId(),'nom'=>$a->getNom(),'role'=>$a->getRole()];
        }, $qb->getQuery()->getResult());
    }
}
