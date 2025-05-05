<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', null, [
            'label' => 'Adresse e-mail',
            'constraints' => [
                new Email([
                    'message' => 'Veuillez rentrer une adresse mail valide.'
                ]),
            ],
        ])
        ->add('phone', null, [
            'label' => 'Numéro de téléphone',
        ])
        ->add('lastName', null, [
            'label' => 'Nom',
        ])
        ->add('firstName', null, [
            'label' => 'Prénom',
        ])
        ->add('agreeTerms', CheckboxType::class, [
            'label' => 'J\'accepte les termes et conditions',
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'Vous devez accepter nos termes et conditions.',
                ]),
            ],
        ])

            
        ->add('plainPassword', PasswordType::class, [
            'mapped' => false,
            'label' => 'Mot de passe',
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un mot de passe.',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                    'max' => 4096,
                ]),
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
