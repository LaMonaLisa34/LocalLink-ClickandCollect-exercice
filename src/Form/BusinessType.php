<?php

namespace App\Form;

use App\Entity\Business;
use App\Entity\Categories;
use App\Entity\Days;
use App\Entity\Label;
use App\Entity\Planning;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusinessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('street_number')
            ->add('street_name')
            ->add('city_name')
            ->add('phone')
            ->add('description')
            ->add('statistic')
            ->add('footprint_carbon')
            // ->add('owner', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('label', EntityType::class, [
            //     'class' => Label::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'category',
            ])
            ->add('plannings', EntityType::class, [
                'class' => Planning::class,
                'choice_label' => function (Planning $planning) {
                    $dayName = $planning->getDays()->getDayName();
                    $openingHour = $planning->getOpeningHour();
                    $closingHour = $planning->getClosingHour();
            
                    return sprintf('%s (Ouverture : %s - Fermeture : %s)', $dayName, $openingHour, $closingHour);
                },
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Business::class,
        ]);
    }
}
