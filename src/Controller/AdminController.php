<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(UserRepository $userRepo, ProductRepository $productRepo, OrderRepository $orderRepo): Response
    {
        return $this->render('admin/index.html.twig', [
            'countUsers' => $userRepo->count([]),
            'countProducts' => $productRepo->count([]),
            'countOrders' => $orderRepo->countTotalOrders(),
            'totalRevenue' => $orderRepo->calculateTotalRevenue(),
        ]);
    }

    #[Route('/statistiques', name: 'admin_statistics')]
    public function statistics(
        OrderRepository $orderRepo, 
        ProductRepository $productRepo, 
        UserRepository $userRepo,
        CategoryRepository $categoryRepo
    ): Response {
        // --- 1. Données du graphique Linéaire (Ventes 7 derniers jours) ---
        $salesData = $orderRepo->getSalesDataLast7Days();
        $labelsSales = [];
        $dataSales = [];
        foreach ($salesData as $row) {
            $labelsSales[] = date('d M', strtotime($row['date']));
            $dataSales[] = $row['total'];
        }

        // --- 2. Données du graphique Camembert (Répartition par Catégorie) ---
        $categories = $categoryRepo->findAll();
        $labelsCat = [];
        $dataCat = [];
        foreach ($categories as $category) {
            $labelsCat[] = $category->getName();
            $dataCat[] = count($category->getProducts());
        }

        return $this->render('admin/statistics.html.twig', [
            'totalOrders' => $orderRepo->countTotalOrders(),
            'totalRevenue' => $orderRepo->calculateTotalRevenue(),
            'totalProducts' => $productRepo->count([]),
            'totalUsers' => $userRepo->count([]),
            // Données pour le JS
            'labelsSales' => json_encode($labelsSales),
            'dataSales' => json_encode($dataSales),
            'labelsCat' => json_encode($labelsCat),
            'dataCat' => json_encode($dataCat),
        ]);
    }

    #[Route('/utilisateurs', name: 'admin_users')]
    public function listUsers(UserRepository $userRepo): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepo->findAll()
        ]);
    }

    #[Route('/produits', name: 'admin_products')]
    public function listProducts(ProductRepository $productRepo): Response
    {
        return $this->render('admin/products.html.twig', [
            'products' => $productRepo->findAll()
        ]);
    }

    #[Route('/utilisateur/promouvoir/{id}', name: 'admin_user_promote')]
    public function promoteToSeller(User $user, EntityManagerInterface $em): Response
    {
        $roles = $user->getRoles();
        if (!in_array('ROLE_SELLER', $roles)) {
            $roles[] = 'ROLE_SELLER';
            $user->setRoles($roles);
            $em->flush();
            $this->addFlash('success', 'L\'utilisateur ' . $user->getEmail() . ' est désormais vendeur.');
        } else {
            $this->addFlash('info', 'Cet utilisateur possède déjà le rôle vendeur.');
        }
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/utilisateur/supprimer/{id}', name: 'admin_user_delete')]
    public function deleteUser(int $id, UserRepository $userRepo, EntityManagerInterface $em): Response
    {
        $user = $userRepo->find($id);
        if ($user === $this->getUser()) {
            $this->addFlash('danger', 'Action impossible : vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('admin_users');
        }
        if ($user) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }
        return $this->redirectToRoute('admin_users');
    }
}