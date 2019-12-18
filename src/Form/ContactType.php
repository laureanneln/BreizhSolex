<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends ApplicationType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
        ->add(
            'firstName', 
            TextType::class, 
            $this->getConfiguration('Prénom *', 'Votre prénom')
        )
        ->add(
            'lastName', 
            TextType::class,
            $this->getConfiguration('Nom *', 'Votre nom')
        )
        ->add(
            'phone', 
            TelType::class,
            $this->getConfiguration('Téléphone *', 'Votre numéro de téléphone')
        )
        ->add(
            'email', 
            EmailType::class,
            $this->getConfiguration('Email *', 'Votre adresse email')
        )
        ->add(
            'message', 
            TextareaType::class,
            $this->getConfiguration('Message *', 'Votre message')
        );
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
