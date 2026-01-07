<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('search');
        $user = $this->getUser();
        $sellerProfile = $user ? $user->getSellerProfile() : null;

        // LOGIQUE DE FILTRAGE
        if ($searchTerm) {
            // Si on recherche, on cherche dans tout le catalogue (ou tu peux restreindre aussi)
            $products = $productRepository->findBySearchTerm($searchTerm);
        } elseif ($sellerProfile) {
            // SI l'utilisateur est un VENDEUR, on ne lui montre que SES produits
            $products = $productRepository->findBy(['seller' => $sellerProfile]);
        } else {
            // SINON (Client ou visiteur), on affiche TOUT
            $products = $productRepository->findAll();
        }

        return $this->render('main/index.html.twig', [
            'products' => $products,
            'searchTerm' => $searchTerm
        ]);
    }
}