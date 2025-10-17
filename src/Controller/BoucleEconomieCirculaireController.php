<?php

namespace App\Controller;

use App\Entity\BoucleEconomieCirculaire;
use App\Form\BoucleEconomieCirculaireType;
use App\Repository\BoucleEconomieCirculaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/boucle/economie/circulaire')]
class BoucleEconomieCirculaireController extends AbstractController
{
    #[Route('/', name: 'app_boucle_economie_circulaire_index', methods: ['GET'])]
    public function index(BoucleEconomieCirculaireRepository $boucleEconomieCirculaireRepository): Response
    {
        return $this->render('boucle_economie_circulaire/index.html.twig', [
            'boucle_economie_circulaires' => $boucleEconomieCirculaireRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_boucle_economie_circulaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $boucleEconomieCirculaire = new BoucleEconomieCirculaire();
        $form = $this->createForm(BoucleEconomieCirculaireType::class, $boucleEconomieCirculaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($boucleEconomieCirculaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_boucle_economie_circulaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('boucle_economie_circulaire/new.html.twig', [
            'boucle_economie_circulaire' => $boucleEconomieCirculaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_boucle_economie_circulaire_show', methods: ['GET'])]
    public function show(BoucleEconomieCirculaire $boucleEconomieCirculaire): Response
    {
        return $this->render('boucle_economie_circulaire/show.html.twig', [
            'boucle_economie_circulaire' => $boucleEconomieCirculaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_boucle_economie_circulaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BoucleEconomieCirculaire $boucleEconomieCirculaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BoucleEconomieCirculaireType::class, $boucleEconomieCirculaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_boucle_economie_circulaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('boucle_economie_circulaire/edit.html.twig', [
            'boucle_economie_circulaire' => $boucleEconomieCirculaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_boucle_economie_circulaire_delete', methods: ['POST'])]
    public function delete(Request $request, BoucleEconomieCirculaire $boucleEconomieCirculaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$boucleEconomieCirculaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($boucleEconomieCirculaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_boucle_economie_circulaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
