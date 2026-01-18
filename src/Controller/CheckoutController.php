<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem; 
use App\Repository\ProductRepository;
use App\Form\CheckoutType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CheckoutController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private RequestStack $requestStack 
    ) {}

    #[Route('/checkout', name: 'app_checkout', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $session = $this->requestStack->getSession();
        $user = $this->getUser();
        $cartName = $user ? 'cart_' . $user->getUserIdentifier() : 'cart_guest';
        $cartData = $session->get($cartName, []); 
        
        if (empty($cartData)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_home');
        }

        $cartWithData = [];
        $total = 0;

        foreach ($cartData as $id => $quantity) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $cartWithData[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unitPrice' => $product->getPrice()
                ];
                $total += $product->getPrice() * $quantity;
            }
        }

        $form = $this->createForm(CheckoutType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            // A. Validation finale du stock avant enregistrement
            foreach ($cartWithData as $item) {
                if ($item['quantity'] > $item['product']->getStock()) {
                    $this->addFlash('danger', 'Le stock a changé. Insuffisant pour : ' . $item['product']->getName());
                    return $this->redirectToRoute('app_checkout');
                }
            }

            // B. Création de la commande
            $order = new Order();
            $fullAddress = sprintf("Destinataire: %s \n Adresse: %s", $formData['fullName'], $formData['shippingAddress']);

            $order->setUser($user);
            $order->setTotal($total); 
            $order->setStatus('Nouvelle');
            $order->setPaymentMethod($formData['paymentMethod']);
            $order->setShippingAddress($fullAddress); 
            $order->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($order);
            
            // C. Création des lignes et mise à jour des stocks
            foreach ($cartWithData as $item) {
                $product = $item['product'];
                
                $orderItem = new OrderItem();
                $orderItem->setOrder($order);
                $orderItem->setProduct($product);
                $orderItem->setQuantity($item['quantity']);
                $orderItem->setUnitPrice($item['unitPrice']);

                $this->entityManager->persist($orderItem);

                // Décrémentation du stock
                $product->setStock($product->getStock() - $item['quantity']);
                $this->entityManager->persist($product);
            }

            $this->entityManager->flush();
            $session->remove($cartName);

            $this->addFlash('success', 'Votre commande n°' . $order->getId() . ' a été passée avec succès !');
            return $this->redirectToRoute('app_home'); 
        }

        return $this->render('checkout/index.html.twig', [
            'checkoutForm' => $form->createView(),
            'items' => $cartWithData, 
            'total' => $total
        ]);
    }
}