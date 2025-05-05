<?php
namespace App\Form;

use App\Entity\Business;
use App\Entity\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AddCommerceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Champs généraux
        $builder
            ->add('name', null, [
                'label' => 'Nom du commerce',
                'attr' => ['class' => 'form-input w-full'],
            ])
            ->add('streetnumber', null, [
                'label' => 'Numéro de rue',
                'data' => '1',
                'attr' => ['class' => 'form-input w-full'],
            ])
            ->add('streetname', null, [
                'label' => 'Nom de rue',
                'data' => 'rue de la loge',
                'attr' => ['class' => 'form-input w-full'],
            ])
            ->add('cityname', null, [
                'label' => 'Ville',
                'data' => 'Montpellier',
                'attr' => ['class' => 'form-input w-full'],
            ])
            ->add('phone', null, [
                'label' => 'Téléphone',
                'data' => '0123456789',
                'attr' => ['class' => 'form-input w-full'],
            ])
            ->add('description', null, [
                'label' => 'Description',
                'data' => 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Nam voluptas illum animi dolores laudantium, ab beatae autem! Laborum eveniet tenetur sequi harum cumque totam molestias aut, cupiditate nobis qui iste?',
                'attr' => ['class' => 'form-textarea w-full'],
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'category', // Attribut de l'entité `Categories`
                'label' => 'Catégorie',
                'placeholder' => 'Sélectionnez une catégorie',
                'attr' => ['class' => 'form-select w-full'],
            ]);

        // Champs pour les jours (non mappés)
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as $day) {
            $builder->add($day . '_opening', TextType::class, [
                
                'label' => ucfirst($day) . ' - Heure d\'ouverture',
                'required' => false,
                'mapped' => false, // Non lié directement à l'entité
                'data' => 'fermé',
                'attr' => [
                    // 'placeholder' => 'Horaire (HH:MM) ou "fermé"', 
                    'class' => 'form-input w-full',
                ],
            ]);

            $builder->add($day . '_closing', TextType::class, [
                'label' => ucfirst($day) . ' - Heure de fermeture',
                'required' => false,
                'mapped' => false, // Non lié directement à l'entité
                'data' => 'fermé',
                'attr' => [
                    // 'placeholder' => 'Horaire (HH:MM) ou "Fermé"',
                    'class' => 'form-input w-full',
                ],
            ]);
        }

        // Champ pour les photos (non mappé)
        // $builder
        // ->add('photo1', FileType::class, [
        //     'label' => 'Photo 1',
        //     'mapped' => false,
        //     'required' => false,
        //     'attr' => [
        //         'accept' => 'image/*',
        //         'class' => 'form-input w-full',
        //     ],
        // ])
        // ->add('photo2', FileType::class, [
        //     'label' => 'Photo 2',
        //     'mapped' => false,
        //     'required' => false,
        //     'attr' => [
        //         'accept' => 'image/*',
        //         'class' => 'form-input w-full',
        //     ],
        // ])
        // ->add('photo3', FileType::class, [
        //     'label' => 'Photo 3',
        //     'mapped' => false,
        //     'required' => false,
        //     'attr' => [
        //         'accept' => 'image/*',
        //         'class' => 'form-input w-full',
        //     ],
        // ])
        // ->add('photo4', FileType::class, [
        //     'label' => 'Photo 4',
        //     'mapped' => false,
        //     'required' => false,
        //     'attr' => [
        //         'accept' => 'image/*',
        //         'class' => 'form-input w-full',
        //     ],
        // ]);

        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Business::class, // Lié à l'entité `Business`
        ]);
    }
}
