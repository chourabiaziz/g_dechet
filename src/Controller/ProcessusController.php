<?php

namespace App\Controller;

use App\Entity\Processus;
use App\Form\ProcessusType;
use App\Repository\ProcessusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/processus')]
class ProcessusController extends AbstractController
{
    #[Route('/', name: 'app_processus_index', methods: ['GET'])]
    public function index(ProcessusRepository $processusRepository): Response
    {
        return $this->render('processus/index.html.twig', [
            'processuses' => $processusRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_processus_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $processu = new Processus();
        $form = $this->createForm(ProcessusType::class, $processu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($processu);
            $entityManager->flush();

            return $this->redirectToRoute('app_processus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('processus/new.html.twig', [
            'processu' => $processu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_processus_show', methods: ['GET'])]
    public function show(Processus $processu): Response
    {
        return $this->render('processus/show.html.twig', [
            'processu' => $processu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_processus_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Processus $processu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProcessusType::class, $processu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_processus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('processus/edit.html.twig', [
            'processu' => $processu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_processus_delete', methods: ['POST'])]
    public function delete(Request $request, Processus $processu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$processu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($processu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_processus_index', [], Response::HTTP_SEE_OTHER);
    }
}
