<?php

namespace App\Controller;

use App\Entity\ProduitRecycle;
use App\Form\ProduitRecycleType;
use App\Repository\ProduitRecycleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produit/recycl')]
class ProduitRecycleController extends AbstractController
{
    #[Route('/', name: 'app_produit_recycl__index', methods: ['GET'])]
    public function index(ProduitRecycleRepository $produitRecycleRepository): Response
    {
        return $this->render('produit_recycle/index.html.twig', [
            'produit_recycl_s' => $produitRecycleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_produit_recycl__new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produitRecycle = new ProduitRecycle();
        $form = $this->createForm(ProduitRecycleType::class, $produitRecycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produitRecycle);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_recycl__index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit_recycle/new.html.twig', [
            'produit_recycl_' => $produitRecycle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_recycl__show', methods: ['GET'])]
    public function show(ProduitRecycle $produitRecycle): Response
    {
        return $this->render('produit_recycle/show.html.twig', [
            'produit_recycl_' => $produitRecycle,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_recycl__edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProduitRecycle $produitRecycle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitRecycleType::class, $produitRecycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_recycl__index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit_recycle/edit.html.twig', [
            'produit_recycl_' => $produitRecycle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_recycl__delete', methods: ['POST'])]
    public function delete(Request $request, ProduitRecycle $produitRecycle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produitRecycle->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produitRecycle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_recycl__index', [], Response::HTTP_SEE_OTHER);
    }
}
