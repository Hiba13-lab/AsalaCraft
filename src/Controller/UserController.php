<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order; // Importation pour la nouvelle action
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\OrderRepository; // Importation pour la nouvelle action
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
#[IsGranted('ROLE_ADMIN')] // Protection : seuls les admins entrent ici pour la gestion des users
final class UserController extends AbstractController
{
    // Méthodes existantes pour l'administration des utilisateurs
    
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }


    // =======================================================
    // NOUVELLE ACTION : Historique des Commandes Client (CdC 7.4)
    // =======================================================

    /**
     * Affiche l'historique des commandes de l'utilisateur connecté.
     * La route commence par '/' pour annuler le préfixe '/user' de la classe.
     */
    #[Route('/account/orders', name: 'app_account_orders', methods: ['GET'])]
    #[IsGranted('ROLE_USER')] // Seul l'utilisateur connecté (client) peut y accéder
    public function orders(OrderRepository $orderRepository): Response
    {
        // On s'assure que l'utilisateur est bien une instance de notre Entité User
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Récupérer toutes les commandes liées à cet utilisateur, triées par date
        $orders = $orderRepository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );

        // Rendu du template (templates/account/orders/index.html.twig)
        return $this->render('account/orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }
}