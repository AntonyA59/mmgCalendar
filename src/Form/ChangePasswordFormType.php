<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password', 'class' => 'form-control mb-3 mdpVerif',
                        'title' => "Pour des raisons de sécurité, votre mot de passe doit contenir au minimum 8 caractères dont 1 lettre majuscule, 1 lettre minuscule, 1 chiffre, et un caractère spécial.",
                        'pattern' => "^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ÿ])(?=.*[0-9])(?=.*[a-zà-ÿA-ZÀ-Ÿ0-9]).{8,}$"
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Le champ ne doit pas être vide',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Votre mot de passe ne doit pas depassé {{ limit }} caractère',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'Nouveau Mot de passe',
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control mb-3 mdpConfirm'],
                    'label' => 'Confirmer votre mot de passe',
                ],
                'invalid_message' => 'Les deux mots de passe doivent être identique',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}