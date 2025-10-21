<?php

namespace App\Controller;

use App\Entity\Acteur;
use App\Form\ActeurType;
use App\Repository\ActeurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/acteur')]
class ActeurController extends AbstractController
{

    #[Route('/', name: 'app_acteur_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('acteur/index.html.twig');
    }

   #[Route('/data', name: 'app_acteur_data', methods: ['GET'])]
    public function data(Request $req, ActeurRepository $repo): JsonResponse
    {
        $q     = $req->query->get('q');
        $sort  = $req->query->get('sort', 'id');
        $dir   = $req->query->get('dir', 'ASC');
        $page  = max(1, (int)$req->query->get('page', 1));
        $limit = max(1, (int)$req->query->get('limit', 10));

        $result = $repo->searchPaginated($q, $sort, $dir, $page, $limit);
        $stats  = $repo->getStats($q);

        return $this->json([
            'items' => $result['items'],
            'meta'  => [
                'total' => $result['total'],
                'page' => $page,
                'limit' => $limit,
                'sort' => $sort,
                'dir' => strtoupper($dir),
            ],
            'stats' => $stats,
        ]);
    }

    #[Route('/export.csv', name: 'app_acteur_export_csv', methods: ['GET'])]
    public function exportCsv(Request $req, ActeurRepository $repo): Response
    {
        $q    = $req->query->get('q');
        $sort = $req->query->get('sort', 'id');
        $dir  = $req->query->get('dir', 'ASC');
        $rows = $repo->searchAllForExport($q, $sort, $dir);

        $response = new StreamedResponse(function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Nom', 'Role']);
            foreach ($rows as $r) {
                fputcsv($out, [$r['id'], $r['nom'], $r['role']]);
            }
            fclose($out);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="acteurs.csv"');
        return $response;
    }

    #[Route('/export.xlsx', name: 'app_acteur_export_xlsx', methods: ['GET'])]
    public function exportXlsx(Request $req, ActeurRepository $repo): Response
    {
        // composer require phpoffice/phpspreadsheet
        $q    = $req->query->get('q');
        $sort = $req->query->get('sort', 'id');
        $dir  = $req->query->get('dir', 'ASC');
        $rows = $repo->searchAllForExport($q, $sort, $dir);

        $roles = $repo->getStats($q)['roles']; // [{role,total}]

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Acteurs');

        // Données
        $sheet->fromArray([['ID','Nom','Role']], null, 'A1');
        $r = 2;
        foreach ($rows as $row) {
            $sheet->fromArray([[$row['id'],$row['nom'],$row['role']]], null, 'A'.$r++);
        }

        // Onglet Stats + Chart
        $statsSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Stats');
        $spreadsheet->addSheet($statsSheet);
        $statsSheet->fromArray([['Role','Total']], null, 'A1');
        $i = 2;
        foreach ($roles as $role) {
            $statsSheet->setCellValue('A'.$i, $role['role'] ?? 'N/A');
            $statsSheet->setCellValue('B'.$i, (int)$role['total']);
            $i++;
        }

        // Pie chart (chart “dans” l’XLSX)
        $dataSeriesLabels = [new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', 'Stats!$B$1', null, 1)];
        $xAxisTickValues  = [new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', 'Stats!$A$2:$A$'.($i-1), null, ($i-2))];
        $dataSeriesValues = [new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', 'Stats!$B$2:$B$'.($i-1), null, ($i-2))];

        $series = new \PhpOffice\PhpSpreadsheet\Chart\DataSeries(
            \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_PIECHART,
            null, range(0, count($dataSeriesValues)-1), $dataSeriesLabels, $xAxisTickValues, $dataSeriesValues
        );

        $layout = new \PhpOffice\PhpSpreadsheet\Chart\Layout();
        $layout->setShowPercent(true)->setShowLegend(true);

        $chart = new \PhpOffice\PhpSpreadsheet\Chart\Chart(
            'Répartition par rôle',
            new \PhpOffice\PhpSpreadsheet\Chart\Title('Répartition par rôle'),
            new \PhpOffice\PhpSpreadsheet\Chart\Legend(\PhpOffice\PhpSpreadsheet\Chart\Legend::POSITION_RIGHT, null, false),
            $series, true, 0, null, null, $layout
        );

        $chart->setTopLeftPosition('D2')->setBottomRightPosition('L20');
        $statsSheet->addChart($chart);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="acteurs.xlsx"');
        return $response;
    }



 
#[Route('/export', name:'app_acteur_export', methods:['GET'])]
public function export(ActeurRepository $repo): Response
{
    $response = new StreamedResponse(function() use ($repo) {
        $handle = fopen('php://output', 'w+');

        // Header CSV
        fputcsv($handle, ['ID','Nom','ROle','Nombre de DEchets']);

        $acteurs = $repo->findAll();
        foreach($acteurs as $a){
            fputcsv($handle, [
                $a->getId(),
                $a->getNom(),
                $a->getRole(),
                count($a->getDechets()),
            ]);
        }

        // Ajouter une section "charts" avec données simples
        fputcsv($handle, []);
        fputcsv($handle, ['# Charts Info']);
        fputcsv($handle, ['ROle','Nombre']);
        $roles = [];
        foreach($acteurs as $a){
            $roles[$a->getRole()] = ($roles[$a->getRole()] ?? 0)+1;
        }
        foreach($roles as $role=>$count){
            fputcsv($handle, [$role,$count]);
        }

        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
    $response->headers->set('Content-Disposition', 'attachment; filename="acteurs.csv"');

    return $response;
}

    #[Route('/new', name: 'app_acteur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $acteur = new Acteur();
        $form = $this->createForm(ActeurType::class, $acteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($acteur);
            $entityManager->flush();

            return $this->redirectToRoute('app_acteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('acteur/new.html.twig', [
            'acteur' => $acteur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_acteur_show', methods: ['GET'])]
    public function show(Acteur $acteur): Response
    {
        return $this->render('acteur/show.html.twig', [
            'acteur' => $acteur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_acteur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Acteur $acteur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActeurType::class, $acteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_acteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('acteur/edit.html.twig', [
            'acteur' => $acteur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_acteur_delete', methods: ['POST'])]
    public function delete(Request $request, Acteur $acteur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$acteur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($acteur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_acteur_index', [], Response::HTTP_SEE_OTHER);
    }
}
