<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Subcategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SubcategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $numbers= $options['numbers'];
        $builder
            ->add('name')
            ->add('position', ChoiceType::class, array(
                'choices'  => $numbers,
                'label'=> 'Choisissez une position'
                ))
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subcategory::class,
            'numbers'=>null
        ]);
    }
}
