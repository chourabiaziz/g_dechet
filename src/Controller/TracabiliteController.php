<?php

namespace App\Controller;

use App\Entity\Tracabilite;
use App\Form\TracabiliteType;
use App\Repository\TracabiliteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tracabilite')]
class TracabiliteController extends AbstractController
{
    #[Route('/', name: 'app_tracabilite_index', methods: ['GET'])]
    public function index(TracabiliteRepository $tracabiliteRepository): Response
    {
        return $this->render('tracabilite/index.html.twig', [
            'tracabilites' => $tracabiliteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tracabilite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tracabilite = new Tracabilite();
        $form = $this->createForm(TracabiliteType::class, $tracabilite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tracabilite);
            $entityManager->flush();

            return $this->redirectToRoute('app_tracabilite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tracabilite/new.html.twig', [
            'tracabilite' => $tracabilite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tracabilite_show', methods: ['GET'])]
    public function show(Tracabilite $tracabilite): Response
    {
        return $this->render('tracabilite/show.html.twig', [
            'tracabilite' => $tracabilite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tracabilite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tracabilite $tracabilite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TracabiliteType::class, $tracabilite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tracabilite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tracabilite/edit.html.twig', [
            'tracabilite' => $tracabilite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tracabilite_delete', methods: ['POST'])]
    public function delete(Request $request, Tracabilite $tracabilite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tracabilite->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tracabilite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tracabilite_index', [], Response::HTTP_SEE_OTHER);
    }
}
