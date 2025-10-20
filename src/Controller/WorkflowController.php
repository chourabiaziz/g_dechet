<?php

namespace App\Controller;
    
use App\Entity\Dechet;
use App\Entity\Processus;
use App\Entity\ProduitRecycle;
use App\Entity\Tracabilite;
use App\Form\DechetType;
use App\Repository\DechetRepository;
use App\Repository\ProcessusRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/work')]
class WorkflowController extends AbstractController
{


  
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/workflow', name: 'app_dechet_workflow', methods: ['GET'])]
    public function workflowOverview(DechetRepository $dechetRepository): Response
    {
        // Statistiques pour les graphiques
        $stats = $dechetRepository->getWorkflowStats();
        
        // Données pour le graphique circulaire
        $etatData = [
            'labels' => ['Déclarés', 'En Processus', 'Transformés'],
            'datasets' => [
                [
                    'data' => [
                        $dechetRepository->count(['etat' => 'declare']),
                        $dechetRepository->count(['etat' => 'en_processus']),
                        $dechetRepository->count(['etat' => 'transforme'])
                    ],
                    'backgroundColor' => ['#ffc107', '#0dcaf0', '#198754']
                ]
            ]
        ];

        return $this->render('dechet/workflow_overview.html.twig', [
            'etapes' => [
                ['nom' => 'Déclaration', 'route' => 'app_dechet_declaration', 'icon' => 'ti ti-file-text'],
                ['nom' => 'Processus', 'route' => 'app_dechet_processus_list', 'icon' => 'ti ti-settings'],
                ['nom' => 'Transformation', 'route' => 'app_dechet_transformation_list', 'icon' => 'ti ti-refresh'],
                ['nom' => 'Tracabilité', 'route' => 'app_dechet_tracabilite_dashboard', 'icon' => 'ti ti-chart-bar'],
            ],
            'stats' => $stats,
            'chartData' => $etatData
        ]);
    }

    #[Route('/workflow/stats', name: 'app_dechet_workflow_stats', methods: ['GET'])]
    public function workflowStats(DechetRepository $dechetRepository): JsonResponse
    {
        // Données pour les graphiques AJAX
        $monthlyData = $dechetRepository->getMonthlyStats();
        
        return $this->json([
            'monthly' => $monthlyData,
            'types' => $dechetRepository->getStatsByType(),
            'efficiency' => $dechetRepository->getTransformationEfficiency()
        ]);
    }

    // ÉTAPE 1: DÉCLARATION DU DÉCHET
    #[Route('/declaration', name: 'app_dechet_declaration', methods: ['GET', 'POST'])]
    public function declaration(Request $request): Response
    {
        $dechet = new Dechet();
        $form = $this->createForm(DechetType::class, $dechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Initialisation du déchet
            $dechet->setEtat('declare');
            $dechet->setDateProduction(new \DateTime());
            
            $this->entityManager->persist($dechet);
            $this->entityManager->flush();

            // Création de la première trace de traçabilité
            $tracabilite = new Tracabilite();
            $tracabilite->setHistorique("Déchet déclaré - Type: " . $dechet->getType() . ", Quantité: " . $dechet->getQuantite());
            $tracabilite->setDateMiseAJour(new \DateTime());
            
            $dechet->setTracabilite($tracabilite);
            $this->entityManager->persist($tracabilite);
            $this->entityManager->flush();

            $this->addFlash('success', 'Déchet déclaré avec succès. ID: ' . $dechet->getId());
            return $this->redirectToRoute('app_dechet_processus_list');
        }

        return $this->render('dechet/declaration.html.twig', [
            'form' => $form->createView(),
            'etape' => 'declaration'
        ]);
    }

    // ÉTAPE 2: LISTE POUR ASSIGNATION DES PROCESSUS
    #[Route('/processus', name: 'app_dechet_processus_list', methods: ['GET'])]
    public function processusList(DechetRepository $dechetRepository): Response
    {
        $dechetsAAssigner = $dechetRepository->findBy(['etat' => 'declare']);

        return $this->render('dechet/processus_list.html.twig', [
            'dechets' => $dechetsAAssigner,
            'etape' => 'processus'
        ]);
    }

    // ÉTAPE 2b: ASSIGNATION AU PROCESSUS (déjà existante)
    #[Route('/{id}/processus/assign', name: 'app_dechet_processus_assign', methods: ['GET', 'POST'])]
    public function assignProcessus(Request $request, Dechet $dechet, ProcessusRepository $processusRepository): Response
    {
        $processusDisponibles = $processusRepository->findAll();

        if ($request->isMethod('POST')) {
            $processusId = $request->request->get('processus_id');
            $processus = $processusRepository->find($processusId);

            if ($processus) {
                // Lier le déchet au processus
                $dechet->addProcessus($processus);
                $dechet->setEtat('en_processus');
                
                // Mettre à jour la traçabilité
                if ($dechet->getTracabilite()) {
                    $historique = $dechet->getTracabilite()->getHistorique();
                    $historique .= "\nAssigné au processus: " . $processus->getNom() . " - " . (new \DateTime())->format('d/m/Y H:i');
                    $dechet->getTracabilite()->setHistorique($historique);
                    $dechet->getTracabilite()->setDateMiseAJour(new \DateTime());
                }

                $this->entityManager->flush();

                $this->addFlash('success', 'Déchet assigné au processus: ' . $processus->getNom());
                return $this->redirectToRoute('app_dechet_processus_list');
            }
        }

        return $this->render('dechet/assign_processus.html.twig', [
            'dechet' => $dechet,
            'processus_disponibles' => $processusDisponibles,
            'etape' => 'processus'
        ]);
    }

    // ÉTAPE 3: LISTE POUR TRANSFORMATION
    #[Route('/transformation', name: 'app_dechet_transformation_list', methods: ['GET'])]
    public function transformationList(DechetRepository $dechetRepository): Response
    {
        $dechetsATransformer = $dechetRepository->findBy(['etat' => 'en_processus']);

        return $this->render('dechet/transformation_list.html.twig', [
            'dechets' => $dechetsATransformer,
            'etape' => 'transformation'
        ]);
    }

    // ÉTAPE 3b: TRANSFORMATION EN PRODUIT RECYCLÉ (déjà existante)
    #[Route('/{id}/transformation', name: 'app_dechet_transformation', methods: ['GET', 'POST'])]
    public function transformation(Request $request, Dechet $dechet): Response
    {
        // Le contrôleur passe 'dechet' (singulier) et non 'dechets' (pluriel)
        if ($dechet->getProcessuses()->isEmpty()) {
            $this->addFlash('warning', 'Veuillez d\'abord assigner un processus à ce déchet.');
            return $this->redirectToRoute('app_dechet_processus_assign', ['id' => $dechet->getId()]);
        }
    
        if ($request->isMethod('POST')) {
            $nomProduit = $request->request->get('nom_produit');
            $typeProduit = $request->request->get('type_produit');
    
            // Créer le produit recyclé
            $produitRecycle = new ProduitRecycle();
            $produitRecycle->setNom($nomProduit);
            $produitRecycle->setType($typeProduit);
            
            // Lier au déchet
            $dechet->setProduitrecycle($produitRecycle);
            $dechet->setEtat('transforme');
            
            // Mettre à jour la traçabilité
            if ($dechet->getTracabilite()) {
                $historique = $dechet->getTracabilite()->getHistorique();
                $historique .= "\nTransformé en produit: " . $nomProduit . " (" . $typeProduit . ") - " . (new \DateTime())->format('d/m/Y H:i');
                $dechet->getTracabilite()->setHistorique($historique);
                $dechet->getTracabilite()->setDateMiseAJour(new \DateTime());
            }
    
            $this->entityManager->persist($produitRecycle);
            $this->entityManager->flush();
    
            $this->addFlash('success', 'Déchet transformé en produit recyclé: ' . $nomProduit);
            return $this->redirectToRoute('app_dechet_tracabilite_view', ['id' => $dechet->getId()]);
        }
    
        return $this->render('dechet/transformation.html.twig', [
            'dechet' => $dechet, // ✅ Variable correcte
            'etape' => 'transformation'
        ]);
    }

    // ÉTAPE 4: DASHBOARD DE TRAÇABILITÉ
    #[Route('/tracabilite', name: 'app_dechet_tracabilite_dashboard', methods: ['GET'])]
    public function tracabiliteDashboard(DechetRepository $dechetRepository): Response
    {
        $derniersDechets = $dechetRepository->findBy([], ['dateProduction' => 'DESC'], 20);
        
        $statsTransformation = [
            'total' => $dechetRepository->count([]),
            'avec_tracabilite' => $dechetRepository->createQueryBuilder('d')
                ->select('COUNT(d.id)')
                ->where('d.tracabilite IS NOT NULL')
                ->getQuery()
                ->getSingleScalarResult(),
            'avec_produit_recycle' => $dechetRepository->createQueryBuilder('d')
                ->select('COUNT(d.id)')
                ->where('d.produitrecycle IS NOT NULL')
                ->getQuery()
                ->getSingleScalarResult(),
        ];

        return $this->render('dechet/tracabilite_dashboard.html.twig', [
            'derniers_dechets' => $derniersDechets,
            'stats' => $statsTransformation,
            'etape' => 'tracabilite'
        ]);
    }

    // ÉTAPE 4b: VISUALISATION DE LA TRAÇABILITÉ (déjà existante)
    #[Route('/{id}/tracabilite', name: 'app_dechet_tracabilite_view', methods: ['GET'])]
    public function tracabiliteView(Dechet $dechet): Response
    {
        return $this->render('dechet/tracabilite.html.twig', [
            'dechet' => $dechet,
            'etape' => 'tracabilite'
        ]);
    }

    #[Route('/{id}/tracabilite/pdf', name: 'app_dechet_tracabilite_export_pdf', methods: ['GET'])]
    public function tracabiliteExportPdf(Dechet $dechet): Response
    {
        if (!class_exists(\Dompdf\Dompdf::class)) {
            $this->addFlash('danger', "La bibliothèque Dompdf n'est pas installée. Exécutez `composer require dompdf/dompdf`.");
            return $this->redirectToRoute('app_dechet_tracabilite_view', ['id' => $dechet->getId()]);
        }

        $html = $this->renderView('dechet/tracabilite_pdf.html.twig', [
            'dechet' => $dechet,
        ]);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();

        $response = new Response($output);
        $response->headers->set('Content-Type', 'application/pdf');
        $filename = sprintf('dechet-%d-tracabilite-%s.pdf', $dechet->getId(), (new \DateTime())->format('Ymd-His'));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    #[Route('/dashboard', name: 'app_dechet_dashboard', methods: ['GET'])]
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
            'quantite_totale' => $dechetRepository->createQueryBuilder('d')
                ->select('SUM(d.quantite)')
                ->getQuery()
                ->getSingleScalarResult() ?? 0,
        ];

        // Données pour les graphiques
        $chartData = [
            'etats' => [
                'labels' => ['Déclarés', 'En Processus', 'Transformés'],
                'data' => [
                    $stats['declares'],
                    $stats['en_processus'],
                    $stats['transformes']
                ],
                'colors' => ['#ffc107', '#0dcaf0', '#198754']
            ],
            'types' => $dechetRepository->getStatsByType(),
            'efficiency' => $dechetRepository->getTransformationEfficiency()
        ];

        $derniersDechets = $dechetRepository->findBy([], ['dateProduction' => 'DESC'], 10);

        return $this->render('dechet/dashboard.html.twig', [
            'stats' => $stats,
            'derniers_dechets' => $derniersDechets,
            'chartData' => $chartData
        ]);
    }

    #[Route('/dashboard/stats', name: 'app_dechet_dashboard_stats', methods: ['GET'])]
    public function dashboardStats(DechetRepository $dechetRepository): Response
    {
        $monthlyStats = $dechetRepository->getMonthlyStats();
        
        return $this->json([
            'monthly' => $monthlyStats,
             'types' => $dechetRepository->getStatsByType()
        ]);
    }

    // LISTE DES DÉCHETS PAR ÉTAPE (déjà existante)
    #[Route('/liste/{etat}', name: 'app_dechet_liste_etat', methods: ['GET'])]
    public function listeParEtat(string $etat, DechetRepository $dechetRepository): Response
    {
        $dechets = $dechetRepository->findBy(['etat' => $etat]);

        $etapesLabels = [
            'declare' => 'Déchets Déclarés',
            'en_processus' => 'Déchets en Processus',
            'transforme' => 'Déchets Transformés'
        ];

        return $this->render('dechet/liste_etat.html.twig', [
            'dechets' => $dechets,
            'etat_courant' => $etat,
            'titre_liste' => $etapesLabels[$etat] ?? 'Déchets'
        ]);
    }
    }