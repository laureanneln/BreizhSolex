<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('old_password', PasswordType::class, $this->getConfiguration("Ancien mot de passe", "Donnez votre mot de classe actuel"))
            ->add('new_password', PasswordType::class, $this->getConfiguration("Nouveau mot de passe", "Donnez votre nouveau mot de classe"))
            ->add('confirm_password', PasswordType::class, $this->getConfiguration("Confirmation du nouveau mot de passe", "Confirmez votre nouveau mot de passe"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
