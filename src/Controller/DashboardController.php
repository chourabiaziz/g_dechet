<?php

namespace App\Controller;

use App\Repository\DechetRepository;
use App\Repository\UserRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
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

        $derniersDechets = $dechetRepository->findBy([], ['dateProduction' => 'DESC'], 10);

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'derniers_dechets' => $derniersDechets,
        ]);
    }

    #[Route('/dashboard/export/pdf', name: 'app_dashboard_export_pdf')]
    public function exportPdf(DechetRepository $dechetRepository): Response
    {
        $dechets = $dechetRepository->findBy([], ['dateProduction' => 'DESC'], 100);

        $html = $this->renderView('dashboard/pdf.html.twig', [
            'dechets' => $dechets,
        ]);

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfOutput = $dompdf->output();
        $filename = sprintf('dashboard-%s.pdf', (new \DateTime())->format('Ymd-His'));
        return new Response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    #[Route('/dashboard/export/csv', name: 'app_dashboard_export_csv')]
    public function exportCsv(DechetRepository $dechetRepository): Response
    {
        $dechets = $dechetRepository->findBy([], ['dateProduction' => 'DESC'], 100);
        $rows = [];
        $headers = ['ID', 'Type', 'Quantité (kg)', 'État', 'Date Production'];
        $rows[] = $headers;

        foreach ($dechets as $d) {
            $rows[] = [
                $d->getId(),
                $d->getType(),
                $d->getQuantite(),
                $d->getEtat(),
                $d->getDateProduction() ? $d->getDateProduction()->format('Y-m-d H:i:s') : '',
            ];
        }

        $escape = function ($value) {
            $v = (string) $value;
            $v = str_replace('"', '""', $v);
            return '"' . $v . '"';
        };

        $csv = "\xEF\xBB\xBF";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map($escape, $row)) . "\r\n";
        }

        $filename = sprintf('dashboard-%s.csv', (new \DateTime())->format('Ymd-His'));
        return new Response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    #[Route('/dashboard/export/excel', name: 'app_dashboard_export_excel')]
    public function exportExcel(DechetRepository $dechetRepository): Response
    {
        $dechets = $dechetRepository->findBy([], ['dateProduction' => 'DESC'], 100);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Dashboard Déchets');

        $headers = ['ID', 'Type', 'Quantité (kg)', 'État', 'Date Production'];
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($col, 1, $header);
            $col++;
        }

        $rowNum = 2;
        foreach ($dechets as $d) {
            $sheet->setCellValueByColumnAndRow(1, $rowNum, $d->getId());
            $sheet->setCellValueByColumnAndRow(2, $rowNum, $d->getType());
            $sheet->setCellValueByColumnAndRow(3, $rowNum, $d->getQuantite());
            $sheet->setCellValueByColumnAndRow(4, $rowNum, $d->getEtat());
            $sheet->setCellValueByColumnAndRow(5, $rowNum, $d->getDateProduction() ? $d->getDateProduction()->format('Y-m-d H:i:s') : '');
            $rowNum++;
        }

        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        $filename = sprintf('dashboard-%s.xlsx', (new \DateTime())->format('Ymd-His'));
        return new Response($excelOutput, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
