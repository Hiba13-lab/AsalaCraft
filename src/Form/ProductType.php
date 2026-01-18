<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Vase en céramique']
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie du produit',
                'placeholder' => '--- Choisir une catégorie ---',
                'attr' => ['class' => 'form-select']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix (en €)',
                'attr' => [
                    'class' => 'form-control',
                    'min' => '0.01', 
                    'step' => '0.01' 
                ]
            ])
            
            // --- Champ Stock ---
            ->add('stock', IntegerType::class, [
                'label' => 'Quantité en stock',
                'attr' => [
                    'class' => 'form-control',
                    'min' => '0', 
                    'step' => '1' 
                ]
            ])
            
            // =========================================================
            // NOUVEAU : CHAMP DE SEUIL D'ALERTE AJOUTÉ
            // =========================================================
            ->add('alertThreshold', IntegerType::class, [
                'label' => 'Seuil d\'alerte stock faible',
                'help' => 'Quantité à partir de laquelle l\'alerte de réapprovisionnement se déclenche.',
                'attr' => [
                    'class' => 'form-control',
                    'min' => '0',
                    'step' => '1'
                ]
            ])
            // =========================================================

            ->add('image', FileType::class, [
                'label' => 'Photo du produit (Fichiers JPG ou PNG uniquement)',
                'mapped' => false, // Important si vous ne mappez pas directement la propriété image à un fichier
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '2M',
                        mimeTypes: ['image/jpeg', 'image/png'],
                        mimeTypesMessage: 'Veuillez uploader une image JPG ou PNG valide (max 2Mo).',
                    )
                ],
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}