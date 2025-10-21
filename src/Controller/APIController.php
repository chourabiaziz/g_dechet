<?php

namespace App\Controller;

use App\Repository\DechetRepository;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\SvgWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class APIController extends AbstractController
{
    #[Route('/api/dechets', name: 'app_dechet_api', methods: ['GET'])]
    public function apiDechets(Request $request, DechetRepository $dechetRepository): JsonResponse
    {
        $filters = [
            'search' => $request->query->get('search', ''),
            'type' => $request->query->get('type', ''),
            'etat' => $request->query->get('etat', ''),
            'date_from' => $request->query->get('date_from', ''),
            'date_to' => $request->query->get('date_to', ''),
        ];
    
        $dechets = $dechetRepository->findByFilters($filters);
        $dechetsData = [];
    
        foreach ($dechets as $dechet) {
            // Générer QR code
            $data = sprintf(
                "Déchet ID: %d\nType: %s\nQuantité: %s kg\nÉtat: %s\nDate Production: %s",
                $dechet->getId(),
                $dechet->getType(),
                $dechet->getQuantite(),
                $dechet->getEtat(),
                $dechet->getDateProduction()->format('d/m/Y H:i')
            );
    
            $result = Builder::create()
                ->writer(new SvgWriter())
                ->data($data)
                ->encoding(new Encoding('UTF-8'))
                ->size(80)
                ->margin(5)
                ->build();
    
            $dataUri = $result->getDataUri();
    
            $dechetsData[] = [
                'id' => $dechet->getId(),
                'type' => $dechet->getType(),
                'quantite' => $dechet->getQuantite(),
                'dateProduction' => $dechet->getDateProduction()->format('d/m/Y H:i'),
                'etat' => $dechet->getEtat(),
                'qr_code' => $dataUri,
                'actions' => $this->generateActions($dechet)
            ];
        }
    
        return $this->json([
            'dechets' => $dechetsData,
            'total' => count($dechetsData)
        ]);
    }
    
    private function generateActions($dechet): array
    {
        $actions = [
            'tracabilite' => $this->generateUrl('app_dechet_tracabilite_view', ['id' => $dechet->getId()])
        ];
    
        if ($dechet->getEtat() == 'declare') {
            $actions['processus'] = $this->generateUrl('app_dechet_processus_assign', ['id' => $dechet->getId()]);
        }
    
        if ($dechet->getEtat() == 'en_processus') {
            $actions['transformation'] = $this->generateUrl('app_dechet_transformation', ['id' => $dechet->getId()]);
        }
    
        return $actions;
    }
}
