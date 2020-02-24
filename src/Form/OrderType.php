<?php

namespace App\Form;

use App\Entity\Status;
use App\Entity\CustomerOrder;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends ApplicationType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add(
                'status',
            EntityType::class, [
            'class' => Status::class, 
                'choice_value' => 'id',
                'choice_label' => 'label'
            ],
            $this->getConfiguration('Statut', 'Statut')
            )
            ->add(
                'tracking',
            TextType::class, [
                'mapped' => false
            ],
            $this->getConfiguration('Numéro de suivi', 'Numéro de suivi du colis')
            ) 
            ->add(
                'delivery',
            DateType::class, [
                'mapped' => false,
                'data' => new \DateTime(),
                'widget' => 'single_text',
            ],
            $this->getConfiguration('Date d\'expédition', 'Date d\'expédition')
            ) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => CustomerOrder::class,
        ]);
    }
}
