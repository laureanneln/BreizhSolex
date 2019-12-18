<?php

namespace App\Form;

use App\Entity\About;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AboutType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',
            TextType::class, 
            $this->getConfiguration('Nom', 'Nom de la section')
        )
            ->add('content',
            TextareaType::class, 
            $this->getConfiguration('Contenu', 'Contenu de la section'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => About::class,
        ]);
    }
}
