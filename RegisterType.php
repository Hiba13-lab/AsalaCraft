<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'attr' => ['placeholder' => 'exemple@mail.com', 'maxlength' => 180],
                'constraints' => [
                    // CORRECTION : Pas de crochets [] dans NotBlank et Email
                    new NotBlank(message: 'L\'email est requis'),
                    new Email(message: 'Veuillez saisir un email valide')
                ]
            ])
            ->add('profile_name', TextType::class, [
                'label' => 'Nom complet ou pseudonyme',
                'attr' => ['maxlength' => 50],
                'constraints' => [
                    new NotBlank(message: 'Le nom de profil est requis'),
                    new Length(
                        min: 2,
                        max: 50,
                        minMessage: 'Le nom est trop court',
                        maxMessage: 'Le nom est trop long'
                    )
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank(message: 'Le mot de passe est requis'),
                    new Length(
                        min: 8,
                        max: 4096,
                        minMessage: 'Votre mot de passe doit faire au moins {{ limit }} caractères'
                    ),
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Je souhaite m\'inscrire en tant que :',
                'choices' => [
                    'Acheteur (Client)' => 'ROLE_USER',
                    'Artisan (Vendeur)' => 'ROLE_SELLER',
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(message: 'Veuillez choisir un type de compte')
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
                'attr' => ['class' => 'btn btn-primary w-100']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}