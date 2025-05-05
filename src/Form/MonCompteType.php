<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class MonCompteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            // ->add('roles')
            // ->add('password')
            ->add('plainPassword', PasswordType::class, [
                'required' => false,
                'mapped' => false,  
                'label' => 'Nouveau mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
            ])
            ->add('phone')
            ->add('firstName')
            ->add('lastName');

    }

    private function deleteAccount(FormBuilderInterface $builder): void
    {
        $builder->add('delete', SubmitType::class, [
            'label' => 'Supprimer mon compte',
            'attr' => [
                'class' => 'bg-red-500 text-white px-4 py-2 rounded',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
