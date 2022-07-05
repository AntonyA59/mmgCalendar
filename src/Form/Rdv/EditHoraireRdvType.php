<?php

namespace App\Form\Rdv;

use DateTime;
use App\Entity\Rdv;
use App\Entity\TypeRdv;
use App\Entity\Prestations;
use App\Entity\PlageHoraire;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditHoraireRdvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('horaire',  EntityType::class, [
                'class' => PlageHoraire::class,
                'attr' => ['class' => "form-select"],

                'required' => true,
                'label' => 'Choisissez votre horaire',
                'choice_label' => function ($horaire) {
                    return $horaire->getHoraire()->format('d/m/Y  H:i ');
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('h')
                        ->select('h')
                        ->orderBy('h.horaire', 'ASC')
                        ->where('h.horaire >= :date')
                        ->setParameter('date', new DateTime('12 hours'))
                        ->andWhere('h.horairePrise = false');
                },
            ])

            ->add('buttonValidate', SubmitType::class, [
                'label' => "Modifier",
                'attr' => ['class' => 'btn btn-primary w-100 btn-lg mb-3 mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rdv::class,
        ]);
    }
}
