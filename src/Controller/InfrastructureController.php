<?php

namespace App\Controller;

use App\Entity\Infrastructure;
use App\Form\InfrastructureType;
use App\Repository\InfrastructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/infrastructure')]
class InfrastructureController extends AbstractController
{
    #[Route('/', name: 'app_infrastructure_index', methods: ['GET'])]
    public function index(InfrastructureRepository $infrastructureRepository): Response
    {
        return $this->render('infrastructure/index.html.twig', [
            'infrastructures' => $infrastructureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_infrastructure_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $infrastructure = new Infrastructure();
        $form = $this->createForm(InfrastructureType::class, $infrastructure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($infrastructure);
            $entityManager->flush();

            return $this->redirectToRoute('app_infrastructure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('infrastructure/new.html.twig', [
            'infrastructure' => $infrastructure,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_infrastructure_show', methods: ['GET'])]
    public function show(Infrastructure $infrastructure): Response
    {
        return $this->render('infrastructure/show.html.twig', [
            'infrastructure' => $infrastructure,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_infrastructure_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Infrastructure $infrastructure, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InfrastructureType::class, $infrastructure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_infrastructure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('infrastructure/edit.html.twig', [
            'infrastructure' => $infrastructure,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_infrastructure_delete', methods: ['POST'])]
    public function delete(Request $request, Infrastructure $infrastructure, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$infrastructure->getId(), $request->request->get('_token'))) {
            $entityManager->remove($infrastructure);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_infrastructure_index', [], Response::HTTP_SEE_OTHER);
    }
}
