<?php

namespace App\Form;

use App\Entity\Product;
use App\Form\ApplicationType;
use Proxies\__CG__\App\Entity\Subcategory;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'image',
                TextType::class,
                $this->getConfiguration('Image', 'Image du produit')
            )
            ->add(
                'name',
                TextType::class,
                $this->getConfiguration('Nom', 'Nom du produit')
            )
            ->add(
                'reference',
                TextType::class,
                $this->getConfiguration('Référence', 'Référence du produit')
            )
            ->add(
                'taxePrice',
                MoneyType::class,
                $this->getConfiguration('Prix', 'Prix du produit')
            )
            ->add(
                'description',
                TextareaType::class,
                $this->getConfiguration('Description', 'Description du produit')
            )
            ->add(
                'height',
                NumberType::class,
                $this->getConfiguration('Hauteur (en cm)', 'Hauteur du produit')
            )
            ->add(
                'width',
                NumberType::class,
                $this->getConfiguration('Largeur (en cm)', 'Largeur du produit')
            )
            ->add(
                'weight',
                NumberType::class,
                $this->getConfiguration('Poids (en g)', 'Poids du produit')
            )
            ->add(
                'subcategory',
                EntityType::class, [
                'class' => Subcategory::class, 
                    'choice_value' => 'id',
                    'choice_label' => 'name'
                ],
                $this->getConfiguration('Sous-catégorie', 'Sous-catégorie')
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
