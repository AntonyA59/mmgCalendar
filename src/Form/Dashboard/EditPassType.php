<?php

namespace App\Form\Dashboard;

use App\Entity\Clients;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class EditPassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les deux mots de passe ne sont pas identique',

                    'first_options' =>
                    [
                        'label' => 'Mot de passe',
                        'attr' => [
                            'placeholder' => 'Votre mot de passe ...',
                            'class' => 'form-control mdpVerif',
                            'title' => "Pour des raisons de sécurité, votre mot de passe doit contenir au minimum 8 caractères dont 1 lettre majuscule, 1 lettre minuscule, 1 chiffre, et un caractère spécial.",
                            'pattern' => "^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ÿ])(?=.*[0-9])(?=.*[a-zà-ÿA-ZÀ-Ÿ0-9]).{8,}$"
                        ]


                    ],

                    'second_options' =>
                    [
                        'label' => 'Retaper votre mot de passe',
                        'attr' => [
                            'placeholder' => 'Retaper mot de passe  ...',
                            'class' => 'form-control mdpConfirm',
                            'pattern' => "^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ÿ])(?=.*[0-9])(?=.*[a-zà-ÿA-ZÀ-Ÿ0-9]).{12,}$",
                            'title' => "Pour des raisons de sécurité, votre mot de passe doit contenir au minimum 12 caractères dont 1 lettre majuscule, 1 lettre minuscule, 1 chiffre, et un caractère spécial."
                        ]
                    ]

                ]

            )
            ->add(
                'buttonValidate',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary mt-3 w-100 btn btn-lg btn-primary mb-3'],
                    'label' => "Confirmer"
                ],

            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Clients::class,
        ]);
    }
}