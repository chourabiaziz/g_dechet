<?php

namespace App\Controller;

use App\Repository\DechetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
          // DASHBOARD DE SUIVI
          #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
          public function dashboard(DechetRepository $dechetRepository): Response
          {
              $stats = [
                  'total' => $dechetRepository->count([]),
                  'declares' => $dechetRepository->count(['etat' => 'declare']),
                  'en_processus' => $dechetRepository->count(['etat' => 'en_processus']),
                  'transformes' => $dechetRepository->count(['etat' => 'transforme']),
                  'avec_produit_recycle' => $dechetRepository->createQueryBuilder('d')
                      ->select('COUNT(d.id)')
                      ->where('d.produitrecycle IS NOT NULL')
                      ->getQuery()
                      ->getSingleScalarResult(),
              ];
      
              // Derniers déchets traités
              $derniersDechets = $dechetRepository->findBy([], ['dateProduction' => 'DESC'], 10);
      
              return $this->render('dashboard/index.html.twig', [
                  'stats' => $stats,
                  'derniers_dechets' => $derniersDechets
              ]);
          }
}
