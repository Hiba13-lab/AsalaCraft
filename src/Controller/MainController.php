<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('search');
        $categoryId = $request->query->get('category'); // On récupère l'ID de la catégorie cliquée
        
        $categories = $categoryRepository->findAll();

        // LOGIQUE DE FILTRAGE
        if ($categoryId) {
            // Si on a cliqué sur un bouton de catégorie
            $products = $productRepository->findBy(['category' => $categoryId]);
        } elseif ($searchTerm) {
            // Si on utilise la barre de recherche
            $products = $productRepository->findBySearchTerm($searchTerm);
        } else {
            // Par défaut, on affiche tout
            $products = $productRepository->findAll();
        }

        return $this->render('main/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'searchTerm' => $searchTerm,
            'currentCategory' => $categoryId // Pour savoir quel bouton est actif
        ]);
    }
}