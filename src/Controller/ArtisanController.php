<?php

namespace App\Controller;

use App\Form\SellerProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; // <--- UTILISE 'Attribute' et pas 'Annotation'

class ArtisanController extends AbstractController
{
    #[Route('/artisan/boutique', name: 'app_artisan_shop_settings')]
    public function shopSettings(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

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
}