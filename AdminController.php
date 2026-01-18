<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')] // Protection globale de la classe
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(): Response
    {
        // Mission 9 : Vérification explicite dans le code
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès réservé aux administrateurs.');

        return $this->render('admin/index.html.twig');
    }

    #[Route('/utilisateurs', name: 'admin_users')]
    public function listUsers(): Response
    {
        // Mission 9 : Utile pour les actions sensibles comme voir la liste des membres
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/users.html.twig');
    }

    #[Route('/utilisateur/supprimer/{id}', name: 'admin_user_delete')]
    public function deleteUser(int $id): Response
    {
        // Exemple type de la Mission 9 : Action de suppression
        // On sécurise l'action avant même de chercher l'utilisateur en BDD
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Ici viendra la logique de suppression plus tard
        $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        
        return $this->redirectToRoute('admin_users');
    }
}