<?php

namespace App\Controller\Admin;


use App\Entity\TypeRdv;
use App\Entity\Prestations;
use App\Form\Dashboard\EditPassType;
use App\Form\Dashboard\EditProfilType;
use App\Form\Dashboard\EditTypeRdvType;
use App\Form\Dashboard\PrestationsType;
use App\Repository\TypeRdvRepository;
use App\Repository\PrestationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class DashboardController extends AbstractController
{
    protected $em;
    protected $prestationsRepository;
    protected $typeRdvRepository;
    protected $requestStack;

    public function __construct(
        EntityManagerInterface $em, 
        PrestationsRepository $prestationsRepository, 
        TypeRdvRepository $typeRdvRepository,
        RequestStack $requestStack)
    {
        $this->em = $em;
        $this->prestationsRepository = $prestationsRepository;
        $this->typeRdvRepository = $typeRdvRepository;
        $this->requestStack = $requestStack;
    }
    /* ----- DEBUT DASHBOARD ADMIN ----- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/dashboard', name: 'dashboard_admin')]

    public function dashBoardAdmin(): Response
    {

        /*---- Début "Ajout de Prestations" ---- */
        $prestation = new Prestations;
        $form_prestation = $this->createForm(PrestationsType::class, $prestation);
        $form_prestation->handleRequest($this->requestStack->getCurrentRequest());

        if ($form_prestation->isSubmitted() && $form_prestation->isValid()) {

            $this->em->persist($prestation);
            $this->em->flush();

            $this->addFlash('message', "Votre prestation a bien été créé!");

            return $this->redirectToRoute('dashboard_admin');
        }
        /*---- Fin "Ajout de Prestations" ---- */

        /*---- Début "Ajout de type de rdv" ---- */
        $typeRdV = new TypeRdv;
        $form_rdv = $this->createForm(EditTypeRdvType::class, $typeRdV);
        $form_rdv->handleRequest($this->requestStack->getCurrentRequest());

        if ($form_rdv->isSubmitted() && $form_rdv->isValid()) {

            $this->em->persist($typeRdV);
            $this->em->flush();

            $this->addFlash('message', "Votre type de rdv a bien été créé!");

            return $this->redirectToRoute('dashboard_admin');
        }
        /*---- Fin "Ajout de type de rdv" ---- */


        return $this->render('dashboard/Admin/DashboardAdmin.html.twig', [
            'FormPrestation' => $form_prestation->createView(),
            'FormTypeRdv' => $form_rdv->createView(),
            'TypesRdv' => $this->typeRdvRepository->findBy(['active' => true]),
            'TypesRdvOff' => $this->typeRdvRepository->findBy(['active' => false]),
            'Prestations' => $this->prestationsRepository->findBy(['active' => true]),
            'PrestationsOff' => $this->prestationsRepository->findBy(['active' => false]),
        ]);
    }

    /*---- Début "Modifier le profil de l'admin ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/dashboard/edit', name: 'dashboard_admin_edit')]

    public function editProfilAdmin(): Response
    {
        $client = $this->getUser();
        $formEdit = $this->createForm(EditProfilType::class, $client);

        $formEdit->handleRequest($this->requestStack->getCurrentRequest());

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {
            $this->em->flush();

            $this->addFlash('message', 'Profil mis à jour');
            return $this->redirectToRoute('dashboard_admin');
        }

        return $this->render('dashboard/EditProfil.html.twig', [
            'formEdit' => $formEdit->createView(),
        ]);
    }
    /*---- Fin "Modifier le profil de l'admin ---- */

    /*---- Début "Modification du mot de passe de l'admin" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/dashboard/editPass', name: 'dashboard_admin_pass')]

    public function editPassAdmin(UserPasswordHasherInterface $encoder): Response
    {
        $client = $this->getUser();
        $formEditPass = $this->createForm(EditPassType::class, $client);
        $formEditPass->handleRequest($this->requestStack->getCurrentRequest());

        if ($formEditPass->isSubmitted() && $formEditPass->isValid()) {
            $hash_password = $encoder->hashPassword($client, $client->getPassword());
            $client->setPassword($hash_password);
            $this->em->flush();

            $this->addFlash('message', 'Profil mis à jour');
            return $this->redirectToRoute('dashboard_admin');
        }

        return $this->render('dashboard/EditPass.html.twig', [
            'formEditPass' => $formEditPass->createView(),
        ]);
    }
    /*---- Fin "Modification du mot de passe de l'admin" ---- */

    /*---- Début "Modification d'une prestation" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/dashboard/editPrestation/{id}', name: 'dashboard_admin_editPrestation', requirements:["id"=>"\d+"])]
    public function editPrestationAdmin($id): Response
    {
        $prestation = $this->prestationsRepository->find($id);
        if (!$prestation) {
            throw new NotFoundHttpException("Cette Prestation n'existe pas");
        }
        $form_prestation = $this->createForm(PrestationsType::class, $prestation);
        $form_prestation->handleRequest($this->requestStack->getCurrentRequest());
        if ($form_prestation->isSubmitted() && $form_prestation->isValid()) {
            $this->em->flush();

            $this->addFlash('message', "Votre prestation a bien été modifié!");

            return $this->redirectToRoute('dashboard_admin');
        }
        return $this->render('dashboard/Admin/EditPrestation.html.twig', [
            'FormPrestation' => $form_prestation->createView(),
        ]);
    }
    /*---- Fin "Modification d'une prestations" ---- */

    /*---- Début "Modification d'un type de rdv" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */

    #[Route('/admin/dashboard/editTypeRdv/{id}', name: 'dashboard_admin_editTypeRdv', requirements:["id"=>"\d+"])]
    public function editTypeRdvAdmin($id): Response
    {
        $typeRdv = $this->typeRdvRepository->find($id);

        if (!$typeRdv) {
            throw new NotFoundHttpException("Ce type de rendez-vous n'existe pas");
        }
        $formTypeRdv = $this->createForm(EditTypeRdvType::class,  $typeRdv);
        $formTypeRdv->handleRequest($this->requestStack->getCurrentRequest());
        if ($formTypeRdv->isSubmitted() && $formTypeRdv->isValid()) {
            $this->em->flush();

            $this->addFlash('message', "Votre type de rdv a bien été modifié!");

            return $this->redirectToRoute('dashboard_admin');
        }
        return $this->render('dashboard/Admin/EditTypeRdv.html.twig', [
            'FormTypeRdv' => $formTypeRdv->createView(),

        ]);
    }
    /*---- Fin "Modification d'un type de rdv" ---- */


    /*---- Début "Désactiver une prestation" ---- */

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/dashboard/desactivePrestation/{id}', name: 'dashboard_admin_desactivePrestation', requirements:["id"=>"\d+"])]
    public function RemovePrestationAdmin($id): Response
    {
        $prestation = $this->prestationsRepository->find($id);
        if (!$prestation) {
            throw new NotFoundHttpException("Cette Prestation n'existe pas");
        }
        $prestation->setActive(false);
        $this->em->flush();
        $this->addFlash('message', 'Prestation desactivé avec succès');

        return $this->redirectToRoute('dashboard_admin');
    }
    /*---- Fin "Desactiver une prestation" ---- */

    /*---- Début "Activer une prestation" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/dashboard/activePrestation/{id}', name: 'dashboard_admin_activePrestation', requirements:["id"=>"\d+"])]
    public function ActivePrestationAdmin($id): Response
    {
        $prestation = $this->prestationsRepository->find($id);
        if (!$prestation) {
            throw new NotFoundHttpException("Cette Prestation n'existe pas");
        }
        $prestation->setActive(true);
        $this->em->flush();
        $this->addFlash('message', 'Prestation réactivé avec succès');

        return $this->redirectToRoute('dashboard_admin');
    }
    /*---- Fin "Activer une prestation" ---- */

    /*---- Début "Desactiver un type de rdv" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/dashboard/desactiveTypeRdv/{id}', name: 'dashboard_admin_desactiveTypeRdv', requirements:["id"=>"\d+"])]
    public function RemoveTypeRdvAdmin($id): Response
    {
        $typeRdv = $this->typeRdvRepository->find($id);

        if (!$typeRdv) {
            throw new NotFoundHttpException("Ce type de rendez-vous n'existe pas");
        }
        $typeRdv->setActive(false);
        $this->em->flush();
        $this->addFlash('message', 'Type de rdv desactivé avec succès');

        return $this->redirectToRoute('dashboard_admin');
    }
    /*---- Fin "Desactiver un type de rdv" ---- */

    /*---- Début "Activer un type de rdv" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/dashboard/ActiveTypeRdv/{id}', name: 'dashboard_admin_activeTypeRdv', requirements:["id"=>"\d+"])]
    public function ActiveTypeRdvAdmin($id): Response
    {
        $typeRdv = $this->typeRdvRepository->find($id);
        if (!$typeRdv) {
            throw new NotFoundHttpException("Ce type de rendez-vous n'existe pas");
        }
        $typeRdv->setActive(true);
        $this->em->flush();
        $this->addFlash('message', 'Type de rendez-vous réactivé avec succès');

        return $this->redirectToRoute('dashboard_admin');
    }
    /*---- Fin "Activer un type de rdv" ---- */



    /* ----- FIN DASHBOARD ADMIN ----- */
}
