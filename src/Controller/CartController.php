<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository) {}

    /**
     * Cette méthode permet de générer le nom de la session du panier.
     * Elle est publique pour être utilisée dans le CheckoutController.
     */
    public function getCartSessionName(): string
    {
        $user = $this->getUser();
        return $user ? 'cart_' . $user->getUserIdentifier() : 'cart_guest';
    }

    #[Route('/', name: 'app_cart_index')]
    public function index(RequestStack $requestStack): Response
    {
        if ($this->getUser() && $this->getUser()->getSellerProfile()) {
            $this->addFlash('error', 'En tant que vendeur, vous ne pouvez pas accéder au panier.');
            return $this->redirectToRoute('app_product_index');
        }

        $session = $requestStack->getSession();
        $cartName = $this->getCartSessionName();
        $cart = $session->get($cartName, []);

        $cartWithData = [];
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $cartWithData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
                $total += $product->getPrice() * $quantity;
            }
        }

        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add')]
    public function add(int $id, RequestStack $requestStack): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Veuillez vous connecter pour ajouter des articles.');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getSellerProfile()) {
            $this->addFlash('error', 'Les vendeurs ne peuvent pas acheter.');
            return $this->redirectToRoute('app_home');
        }

        $session = $requestStack->getSession();
        $cartName = $this->getCartSessionName();
        $cart = $session->get($cartName, []);

        $cart[$id] = ($cart[$id] ?? 0) + 1;

        $session->set($cartName, $cart);
        $this->addFlash('success', 'Produit ajouté !');
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(int $id, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $cartName = $this->getCartSessionName();
        $cart = $session->get($cartName, []);

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set($cartName, $cart);
        return $this->redirectToRoute('app_cart_index');
    }
}