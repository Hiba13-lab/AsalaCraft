<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\SellerProfileType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/artisan')]
#[IsGranted('ROLE_SELLER')]
class ArtisanController extends AbstractController
{
    #[Route('/boutique', name: 'app_artisan_shop_settings')]
    public function shopSettings(Request $request, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $sellerProfile = $user->getSellerProfile();

        if (!$sellerProfile) {
            $this->addFlash('error', 'Profil vendeur introuvable.');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(SellerProfileType::class, $sellerProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Boutique mise à jour !');
        }

        return $this->render('artisan/shop_settings.html.twig', [
            'shopForm' => $form->createView(),
        ]);
    }

    #[Route('/orders', name: 'app_seller_orders', methods: ['GET'])]
    public function sellerOrders(OrderRepository $orderRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $orders = $orderRepository->findOrdersBySeller($user);

        return $this->render('artisan/dashboard/orders_index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/orders/{id}/status', name: 'app_seller_order_update_status', methods: ['POST'])]
    public function updateOrderStatus(Order $order, Request $request, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $isSellerOfOrder = false;

        // On vérifie si un des produits de la commande appartient au vendeur connecté
        foreach ($order->getOrderItems() as $item) {
            // Utilisation de getSeller() car la propriété dans Product est $seller
            if ($item->getProduct() && $item->getProduct()->getSeller() && $item->getProduct()->getSeller()->getUser() === $user) {
                $isSellerOfOrder = true;
                break;
            }
        }

        if (!$isSellerOfOrder) {
            $this->addFlash('danger', 'Accès refusé à cette commande.');
            return $this->redirectToRoute('app_seller_orders');
        }
        
        $newStatus = $request->request->get('new_status');
        $validStatuses = ['Nouvel(le)', 'En traitement', 'Expédiée', 'Terminée', 'Annulée'];

        if ($newStatus && in_array($newStatus, $validStatuses)) {
            $order->setStatus($newStatus);
            $em->flush();
            $this->addFlash('success', "Statut mis à jour : " . $newStatus);
        }

        return $this->redirectToRoute('app_seller_orders');
    }
}