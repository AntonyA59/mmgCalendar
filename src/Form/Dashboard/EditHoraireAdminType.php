<?php

namespace App\Form\Dashboard;

use App\Entity\PlageHoraire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class EditHoraireAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('horaire', DateTimeType::class, [
                'date_widget' => 'single_text',
                'attr' => ['class' => 'form-control mb-3'],
                'label' => ' '
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
