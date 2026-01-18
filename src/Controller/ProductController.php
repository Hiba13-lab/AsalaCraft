<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/product')]
final class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $sellerProfile = $user ? $user->getSellerProfile() : null;

        if (!$sellerProfile) {
            $this->addFlash('error', 'Accès réservé aux vendeurs.');
            return $this->redirectToRoute('app_home');
        }

        // On force le filtrage par l'ID du profil vendeur connecté
        $products = $productRepository->findBySellerId($sellerProfile->getId());

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $sellerProfile = $user ? $user->getSellerProfile() : null;

        if (!$sellerProfile) return $this->redirectToRoute('app_login');

        $product = new Product();
        $product->setSeller($sellerProfile); // Liaison automatique au vendeur
        
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug($slugger->slug($product->getName())->lower());

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = $slugger->slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME)).'-'.uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move($this->getParameter('products_directory'), $newFilename);
                $product->setImage($newFilename);
            }
            
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté !');
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/show/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', ['product' => $product]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $sellerProfile = $user ? $user->getSellerProfile() : null;

        if (!$sellerProfile || $product->getSeller() !== $sellerProfile) {
            throw $this->createAccessDeniedException("Ce produit ne vous appartient pas.");
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug($slugger->slug($product->getName())->lower());
            $entityManager->flush();
            $this->addFlash('success', 'Produit mis à jour.');
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', ['form' => $form->createView(), 'product' => $product]);
    }

    #[Route('/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $sellerProfile = $user ? $user->getSellerProfile() : null;

        if (!$sellerProfile || $product->getSeller() !== $sellerProfile) {
            throw $this->createAccessDeniedException("Action interdite.");
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
            $this->addFlash('success', 'Produit supprimé.');
        }

        return $this->redirectToRoute('app_product_index');
    }
}