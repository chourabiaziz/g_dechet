<?php

namespace App\Controller;

use App\Entity\Dechet;
use App\Form\DechetType;
use App\Repository\DechetRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\SvgWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dechet')]
class DechetController extends AbstractController
{
    #[Route('/test-mail', name: 'test_mail')]
    public function testMail(MailService $mailService): Response
    {
        
        $mailService->sendMail(
            'chourabiaziz007@gmail.com',
            'Test d’envoi depuis Longevity Plus',
            '<h1>Ceci est un test</h1><p>Envoi réussi 🚀</p>'
        );

        return new Response('Email envoyé avec succès !');
    }
    #[Route('/', name: 'app_dechet_index', methods: ['GET'])]
    public function index(DechetRepository $dechetRepository): Response
    {
        $dechets = $dechetRepository->findAll();
        $dechetsWithQr = [];
    
        foreach ($dechets as $dechet) {
            // Créer les données pour le QR code
            $data = sprintf(
                "Déchet ID: %d\nType: %s\nQuantité: %s kg\nÉtat: %s\nDate Production: %s",
                $dechet->getId(),
                $dechet->getType(),
                $dechet->getQuantite(),
                $dechet->getEtat(),
                $dechet->getDateProduction()->format('d/m/Y H:i')
            );
    
            // Générer le QR code
            $result = Builder::create()
                ->writer(new SvgWriter())
                ->data($data)
                ->encoding(new Encoding('UTF-8'))
               // ->errorCorrectionLevel(ErrorCorrectionLevel::HIGH)
                ->size(80) // Taille réduite pour le tableau
                ->margin(5)
                ->build();
    
            $dataUri = $result->getDataUri();
    
            $dechetsWithQr[] = [
                'dechet' => $dechet,
                'qr_code' => $dataUri
            ];
        }
    
        return $this->render('dechet/index.html.twig', [
            'dechets_with_qr' => $dechetsWithQr,
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
    #[Route('/new', name: 'app_dechet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dechet = new Dechet();
        $form = $this->createForm(DechetType::class, $dechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dechet);
            $entityManager->flush();

            return $this->redirectToRoute('app_dechet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dechet/new.html.twig', [
            'dechet' => $dechet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dechet_show', methods: ['GET'])]
    public function show(Dechet $dechet): Response
    {
        return $this->render('dechet/show.html.twig', [
            'dechet' => $dechet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dechet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dechet $dechet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DechetType::class, $dechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dechet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dechet/edit.html.twig', [
            'dechet' => $dechet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dechet_delete', methods: ['POST'])]
    public function delete(Request $request, Dechet $dechet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dechet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($dechet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dechet_index', [], Response::HTTP_SEE_OTHER);
    }
}
