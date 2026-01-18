<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom et Prénom',
                'constraints' => [
                    // CORRECTION ICI : Utilisez l'argument nommé 'message'
                    new NotBlank(message: 'Le nom est obligatoire'),
                ],
            ])
            ->add('shippingAddress', TextareaType::class, [
                'label' => 'Adresse complète de livraison',
                'constraints' => [
                    // CORRECTION ICI
                    new NotBlank(message: 'L\'adresse est obligatoire'),
                ],
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'label' => 'Mode de paiement',
                'choices' => [
                    'Espèces à la livraison' => 'Espèces à la livraison',
                    'Carte Bancaire' => 'Carte Bancaire',
                ],
                'expanded' => true,
                'multiple' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, 
        ]);
    }
}