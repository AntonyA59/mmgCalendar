<?php

namespace App\Form\Inscription;

use App\Entity\Clients;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ClientsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

        ->add(
            'nom',
            TextType::class,
            [
                'attr' =>  ['placeholder' => 'Votre nom ...', 'class' => 'form-control mb-3', 'autofocus' => true],
                'label' => 'Nom (obligatoire)'
            ]
        )

        ->add(
            'prenom',
            TextType::class,
            [
                'attr' => ['placeholder' => 'Votre prénom ...', 'class' => 'form-control mb-3'],
                'label' => 'Prénom (obligatoire)'
            ]
        )

        ->add(
            'societe',
            TextType::class,
            [
                'attr' => ['placeholder' => 'Votre société/entreprise ...', 'class' => 'form-control mb-3'],
                'label' => 'Votre Entreprise/Société (obligatoire)'
            ]
        )

        ->add(
            'email',
            RepeatedType::class,
            [
                'type' => EmailType::class,
                'invalid_message' => 'Les deux adresse e-mail ne sont pas identique',

                'first_options' =>
                [
                    'label' => 'Adresse E-mail (obligatoire)',
                    'attr' => ['placeholder' => 'Votre adresse E-mail ...', 'class' => 'form-control mb-3']
                ],



                'second_options' =>
                [
                    'label' => 'Retaper votre adresse E-mail',
                    'attr' => ['placeholder' => 'Retaper votre adresse E-mail ...', 'class' => 'form-control mb-3']
                ]
            ]
        )



        ->add(
            'password',
            RepeatedType::class,
            [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe ne sont pas identique',

                'first_options' =>
                [
                    'label' => 'Mot de passe (obligatoire)',
                    'attr' => [
                        'placeholder' => 'Votre mot de passe ...',
                        'class' => 'form-control  mdpVerif',
                        'title' => "Pour des raisons de sécurité, votre mot de passe doit contenir au minimum 8 caractères dont 1 lettre majuscule, 1 lettre minuscule, 1 chiffre, et un caractère spécial.",
                        'pattern' => "^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ÿ])(?=.*[0-9])(?=.*[a-zà-ÿA-ZÀ-Ÿ0-9]).{8,}$"
                    ]


                ],

                'second_options' =>
                [
                    'label' => 'Retaper votre mot de passe',
                    'attr' => [
                        'placeholder' => 'Retaper mot de passe  ...',

                        'class' => 'form-control mb-3 mdpConfirm',
                        'pattern' => "^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ÿ])(?=.*[0-9])(?=.*[a-zà-ÿA-ZÀ-Ÿ0-9]).{8,}$",
                        'title' => "Pour des raisons de sécurité, votre mot de passe doit contenir au minimum 8 caractères dont 1 lettre majuscule, 1 lettre minuscule, 1 chiffre, et un caractère spécial."
                    ]
                ]

            ]
        )

        ->add(
            'telephone',
            TelType::class,
            [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Votre Numéro de telephone ...', 'class' => 'form-control mb-3',
                    'pattern' => "^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$"
                ],
                'label' => 'Numéro de telephone (facultatif)'

            ]
        )
        ->add('RGPD', CheckboxType::class,
            [   
                'attr' => ['class' => 'form-check mb-3'],
                'label' => "J'accepte que mes données personnelles saisies dans ce formulaire soient utilisées dans le cadre d'une prise de contact. (obligatoire)",
                'required'=> true
            ])
            
        ->add('captcha', CaptchaType::class, [
            'mapped' => false,
            'attr' => ['class' => 'form-control'],
            'label' => 'Retaper le code dans l\'image ci-dessous (obligatoire)',
            'width' => 200,
            'height' => 60,
            'length' => 7 
        ])
        ->add(
            'buttonValidate',
            SubmitType::class,
            [
                'attr' => ['class' => 'btn btn-blueMmg mt-3 w-100 btn btn-lg btn-primary mb-3'],
                'label' => "S'inscrire"
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