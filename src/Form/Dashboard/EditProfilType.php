<?php

namespace App\Form\Dashboard;

use App\Entity\Clients;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Regex;

class EditProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('societe', TextType::class, [
                'label' => 'Votre Entreprise/Société',
                'attr' => ['placeholder' => 'Votre société/entreprise ...', 'class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le nom votre société/entreprise',
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre nom ...', 'class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom',
                    ]),
                ],

            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom',
                'attr' => ['placeholder' => 'Votre prénom ...', 'class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre prénom',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse E-mail',
                'attr' => ['placeholder' => 'Votre adresse E-mail ...', 'class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre e-mail',
                    ]),
                    new Email(['message' => 'Cette adresse e-mail est invalide'])
                ],
            ])

            ->add(
                'telephone',
                TelType::class,
                [
                    'required' => false,
                    'label' => 'Numéro de téléphone',
                    'attr' => ['placeholder' => 'Votre numéro de téléphone ...', 'class' => 'form-control mb-3'],
                    'constraints' => [
                        new Regex([
                            'pattern' => '^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$',
                            'message' => 'Ce numéro de téléphone est invalide'
                        ]),
                    ],
                ]
            )
            ->add('buttonValidate', SubmitType::class, [
                'label' => "Modifier",
                'attr' => ['class' => 'btn btn-primary mt-3 w-100 btn-lg mb-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Clients::class,
        ]);
    }
}
