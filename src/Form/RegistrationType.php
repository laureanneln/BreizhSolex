<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Title;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends ApplicationType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            // ->add(
            //     'title', 
            //     EntityType::class, [
            //         'class' => Title::class,
            //         'choice_value' => 'id',
            //         'choice_label' => 'label',
            //         'expanded' => true,
            //     ]
            // // )
            ->add(
                'firstName', 
                TextType::class, 
                $this->getConfiguration('Prénom', "Votre prénom")
            )
            ->add(
                'lastName', 
                TextType::class, 
                $this->getConfiguration('Nom', 'Votre nom de famille')
            )
            ->add(
                'email', 
                EmailType::class, 
                $this->getConfiguration('Email', 'Votre adresse email')
            )
            ->add(
                'password', 
                PasswordType::class, 
                $this->getConfiguration('Mot de passe', 'Votre mot de passe')
            )
            ->add(
                'passwordConfirm', 
                PasswordType::class, 
                $this->getConfiguration('Confirmation de mot de passe', 'Veuillez confirmer votre mot de passe')
            );
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
