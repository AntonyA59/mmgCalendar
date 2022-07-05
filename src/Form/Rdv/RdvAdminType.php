<?php

namespace App\Form\Rdv;

use DateTime;
use App\Entity\Rdv;
use App\Entity\Clients;
use App\Entity\TypeRdv;
use App\Entity\Prestations;
use App\Entity\PlageHoraire;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RdvAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client',  EntityType::class, [
                'class' => Clients::class,
                'attr' => ['class' => "form-select mb-3"],
                'label' => 'Choisissez votre client (E-mail)',
                'choice_label' => 'email',

                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->select('c')
                        ->where('c.roles LIKE :roles')
                        ->setParameter('roles', '%"' . "ROLE_USER" . '"%');
                },
            ])
            ->add('description', CKEditorType::class, [
                'attr' => [
                    'class' => "form-control mb-3"
                ],
                'label' => 'Votre Message',
                'required' => true
            ])
            ->add('prestations', EntityType::class, [
                'class' => Prestations::class,
                'attr' => ['class' => "form-select"],
                'label' => 'Choisissez le type de prestation',
                'choice_label' => 'type_prestations',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->select('p')
                        ->where('p.active = true');
                }
            ])
            ->add('TypeRdv', EntityType::class, [
                'class' => TypeRdv::class,
                'attr' => ['class' => "form-select mb-3"],
                'label' => 'Choisissez le type de rendez-vous',
                'choice_label' => 'TypeRdv',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->select('t')
                        ->where('t.active = true');
                }

            ])
            ->add('horaire',  EntityType::class, [
                'class' => PlageHoraire::class,
                'attr' => ['class' => "form-select mb-3"],
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
                'label' => "Envoyer",
                'attr' => ['class' => 'btn btn-primary mt-3 w-100 btn-lg mb-3']
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rdv::class,
        ]);
    }
}
