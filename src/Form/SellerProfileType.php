<?php

namespace App\Form;

use App\Entity\SellerProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellerProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shopName', TextType::class, [
                'label' => 'Nom de la boutique',
                'attr' => ['placeholder' => 'Ex: Les Trésors d\'Asala']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de votre savoir-faire',
                'required' => false,
                'attr' => ['rows' => 5]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse / Ville',
                'required' => false
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone de contact',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SellerProfile::class,
        ]);
    }
}