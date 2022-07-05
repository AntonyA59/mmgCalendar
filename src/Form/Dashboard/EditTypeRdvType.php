<?php

namespace App\Form\Dashboard;

use App\Entity\TypeRdv;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditTypeRdvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type_rdv', TextType::class, [
                'attr' => ['placeholder' => 'Ajouter un type de rdv ...', 'class' => 'form-control mb-3'],
                'label' => 'Vos type de rdv'])

            ->add(
                'buttonValidate',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-primary mt-3 w-100 btn btn-lg btn-primary mb-3'],
                    'label' => "Ajouter un type de rdv"
                ],

            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TypeRdv::class,
        ]);
    }
}
