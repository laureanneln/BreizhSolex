<?php

namespace App\Form;

use App\Form\ItemType;
use App\Entity\CustomerOrder;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class InvoiceType extends ApplicationType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
        ->add(
            'items', 
            CollectionType::class, [
            'entry_type' => ItemType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ])
        ->add(
            'firstName', 
            TextType::class, array (
                'mapped' => false,
                'label' => 'Prénom',
                'attr' => array (
                    'placeholder' => 'Prénom du client'
                )
            )
        )
        ->add(
            'lastName', 
            TextType::class, array (
                'mapped' => false,
                'label' => 'Nom',
                'attr' => array (
                    'placeholder' => 'Nom du client'
                )
            )
        )
        ->add(
            'email', 
            EmailType::class, array (
                'mapped' => false,
                'label' => 'Email',
                'attr' => array (
                    'placeholder' => 'Email du client'
                )
            )
        )
        ->add(
            'phone', 
            TelType::class, array (
                'mapped' => false,
                'label' => 'Téléphone',
                'attr' => array (
                    'placeholder' => 'Téléphone du client'
                )
            )
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerOrder::class,
        ]);
    }
}
