<?php

namespace App\Form\Dashboard;

use App\Entity\Prestations;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PrestationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('typePrestations', TextType::class, [
                'label' => 'Ajouter votre prestation',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Ajouter une prestation...'
                ]
            ])
            ->add(
                'buttonValidate',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary mt-3 w-100 btn btn-lg btn-primary mb-3'],
                    'label' => "Ajouter une prestation"
                ],

            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prestations::class,
        ]);
    }
}