<?php

namespace App\Form\Dashboard;

use NumberFormatter;
use App\Entity\PlageHoraire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\Range;

class PlageHoraireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('horaire', DateTimeType::class, [
                'date_widget'=> 'single_text',
                'attr' => ['class' => 'form-control mb-3'],
                'label' => ' '
            ])
            
            ->add('iteration', NumberType::class, [
                'html5' => true,
                'label' => 'Ajouter des jours ? (Entre 0 et 6 jours)',
                'attr' => ['class' => 'form-control mb-3',
            ],
                'mapped' => false,
                'required' => false,
                'rounding_mode' => NumberFormatter::ROUND_CEILING,
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 6,
                        'notInRangeMessage' => "Vous devez taper un chiffre entre {{ min }} et {{ max }} jours"
                    ]),
                ],
            ])
                
            ->add(
                'buttonValidate',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary mt-3 w-100 btn btn-lg btn-primary mb-3'],
                    'label' => "Ajouter une horaire"
                ]

            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlageHoraire::class,
        ]);
    }
}