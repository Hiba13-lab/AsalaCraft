<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category; // IMPORTATION de l'entité Category
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // IMPORTATION du type de champ de relation
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
            // NOUVEAU : Champ Catégorie ajouté ici
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', // Affiche le nom de la catégorie dans la liste
                'label' => 'Catégorie du produit',
                'placeholder' => '--- Choisir une catégorie ---',
                'attr' => ['class' => 'form-select'] // Utilisation de form-select pour Bootstrap 5
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix (en €)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Quantité en stock',
                'attr' => ['class' => 'form-control']
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo du produit (Fichiers JPG ou PNG uniquement)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '2M',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                        ],
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