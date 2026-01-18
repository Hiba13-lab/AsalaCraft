<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\SellerProfile;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route; // Correction ici pour Symfony 7/8

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roleChoisi = $form->get('roles')->getData();
            $user->setRoles([$roleChoisi]);

            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));

            if ($roleChoisi === 'ROLE_SELLER') {
                $seller = new SellerProfile();
                $seller->setUser($user);
                $seller->setShopName("Boutique de " . $user->getProfileName());
                $seller->setCreatedAt(new \DateTimeImmutable());
                $em->persist($seller);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Compte créé ! Connectez-vous.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}