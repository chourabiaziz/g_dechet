<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\PlanRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route('', name: 'app_user_index')]
    public function index(UserRepository $ur): Response
    {

        return $this->render('user/index.html.twig', [
            'users' => $ur->findCoachesByRegistrationDate(),
        ]);
    }

    #[Route('/new', name: 'app_user_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRegistrationDate(new \DateTime());
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_show')]
    public function show(User $user): Response
    {

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/profile', name: 'app_user_profile')]
    public function profile(User $user): Response
    {
        if ($this->getUser() !== $user) {
            throw $this->createAccessDeniedException('You can only access your own profile.');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit/coach', name: 'app_user_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $em): Response
    {

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/{id}/block', name: 'app_user_block', methods: ['POST'])]
    public function block(User $user, EntityManagerInterface $em): Response
    {

        $user->setIsBlocked(!$user->getIsBlocked());
        $em->flush();

        $this->addFlash('success', $user->getIsBlocked() ? 'User blocked successfully.' : 'User unblocked successfully.');

        return $this->redirectToRoute('app_user_index');
    }

   

    #[Route('/users/search', name: 'user_search')]
    public function searchUsers(Request $request, UserRepository $userRepository): JsonResponse
    {

        $term = $request->query->get('q', '');
        
        if (strlen($term) < 2) {
            return new JsonResponse(['results' => []]);
        }
    
        $users = $userRepository->createQueryBuilder('u')
            ->where('u.name LIKE :term OR u.email LIKE :term  OR u.prename LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    
        $results = [];
        foreach ($users as $user) {
            $results[] = [
                'id' => $user->getId(),
                'text' => $user->getFullName() . ' (' . $user->getEmail() . ')'
            ];
        }
    
        return new JsonResponse(['results' => $results]);
    }



 




}