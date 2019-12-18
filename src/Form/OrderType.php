<?php

namespace App\Form;

use App\Entity\Status;
use App\Entity\CustomerOrder;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status',
            EntityType::class, [
            'class' => Status::class, 
                'choice_value' => 'id',
                'choice_label' => 'label'
            ],
            $this->getConfiguration('Sous-catégorie', 'Sous-catégorie')
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
