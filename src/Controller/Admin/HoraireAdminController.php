<?php

namespace App\Controller\Admin;

use DateInterval;
use DateTime;
use App\Entity\PlageHoraire;
use App\Form\Dashboard\EditHoraireAdminType;
use App\Form\Dashboard\PlageHoraireType;
use App\Repository\PlageHoraireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class HoraireAdminController extends AbstractController
{
    protected $requestStack;
    protected $em;
    protected $plageHoraireRepository;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $em,
        PlageHoraireRepository $plageHoraireRepository
    ) {
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->plageHoraireRepository = $plageHoraireRepository;
    }
    /*---- Début "Ajout d'une horaire" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/horaire', name: 'admin_horaire')]
    public function ajoutHoraire(): Response
    {
        // On instancie une nouvelle horaire
        $horaire = new PlageHoraire;
        // On crée le formulaire et on le stocke dans une variable
        $form_horaire = $this->createForm(PlageHoraireType::class, $horaire);
        // La méthode handleRequest permet détecter quand le formulaire a été soumis
        $form_horaire->handleRequest($this->requestStack->getCurrentRequest());

        // On prend les informations du champ pour ajouter des jours et on le stocke dans une variable
        $count = ($form_horaire->get('iteration')->getData()) - 1;
        
        // On prend la date selectionnée et on le stocke dans une variable
        $date = $form_horaire->get('horaire')->getData();
        if ($form_horaire->isSubmitted() && $form_horaire->isValid()) {
            //Si le champ "ajouter des jours" est vide
            if ($count === null) {
                $this->em->persist($horaire);
                $this->em->flush();
            } else {
                $this->em->persist($horaire);
                $this->em->flush();
                
                for ($i = 0; $i <= $count; $i++) {
                    $horaires = new PlageHoraire;
                    $horaires->setHoraire($date->add(new DateInterval('P1D')));
                    $this->em->persist($horaires);
                    $this->em->flush();
                }
            }

            $this->addFlash('message', "Votre plage horaire a bien été créé!");

            return $this->redirectToRoute('admin_horaire');
        }


        return $this->render('PageHoraireAdmin/PageHoraire.html.twig', [
            'form' => $form_horaire->createView(),
            'PlageHoraires' => $this->plageHoraireRepository->findDateAfterNow(),
            'HorairePasses' => $this->plageHoraireRepository->findDateBeforeNow()
        ]);
    }
    /*---- Fin "Ajout d'une horaire" ---- */


    /*---- Début "Modification d'une plage horaire" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/horaire/editPlageHoraire/{id}', name: 'admin_horaire_editPlageHoraire', requirements: ["id" => "\d+"])]
    public function editPlageHoraireAdmin($id): Response
    {

        $plageHoraire = $this->plageHoraireRepository->find($id);
        if (!$plageHoraire) {
            throw new NotFoundHttpException("Cette plage horaire n'existe pas");
        }
        $formPlageHoraire = $this->createForm(EditHoraireAdminType::class,  $plageHoraire);
        $formPlageHoraire->handleRequest($this->requestStack->getCurrentRequest());
        if ($formPlageHoraire->isSubmitted() && $formPlageHoraire->isValid()) {
            $this->em->flush();

            $this->addFlash('message', "Votre plage horaire a bien été modifié!");

            return $this->redirectToRoute('admin_horaire');
        }
        return $this->render('PageHoraireAdmin/EditPlageHoraire.html.twig', [
            'FormPlageHoraire' => $formPlageHoraire->createView(),

        ]);
    }
    /*---- Fin "Modification d'une plage horaire" ---- */

    /*---- Début "Supprimer une plage horaire" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/horaire/removePlageHoraire/{id}', name: 'admin_horaire_removePlageHoraire', requirements: ["id" => "\d+"])]
    public function RemovePlageHoraireAdmin($id): Response
    {
        $plageHoraire = $this->plageHoraireRepository->find($id);
        if (!$plageHoraire) {
            throw new NotFoundHttpException("Cette plage horaire n'existe pas");
        }
        $this->em->remove($plageHoraire);
        $this->em->flush();
        $this->addFlash('message', 'Horaire supprimée avec succès');

        return $this->redirectToRoute('admin_horaire');
    }
    /*---- Fin "Supprimer une plage horaire" ---- */
}
