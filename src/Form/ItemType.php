<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\Product;
use App\Form\ApplicationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends ApplicationType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class, 
                    'choice_value' => 'id',
                    'choice_label' => 'name'
                ]
            )
            ->add('quantity', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Quantité',
                    'value' => 1
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}
