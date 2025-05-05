<?php

namespace App\Form;

use App\Entity\Business;
use App\Entity\Days;
use App\Entity\Planning;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('opening_hour')
            ->add('closing_hour')
            ->add('days', EntityType::class, [
                'class' => Days::class,
                'choice_label' => 'day_name',
            ])
            // ->add('business', EntityType::class, [
            //     'class' => Business::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
                
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
        ]);
    }
}
