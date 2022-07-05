<?php

namespace App\Controller;


use App\Form\Rdv\EditHoraireRdvType;
use App\Repository\PlageHoraireRepository;
use App\Repository\RdvRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class RdvController extends AbstractController
{
    protected $em;
    protected $rdv;
    protected $mailer;
    protected $security;

    public function __construct(
        EntityManagerInterface $em, 
        RdvRepository $rdv, 
        SendMailService $mailer, 
        Security $security)
    {
        $this->em = $em;
        $this->rdv = $rdv;
        $this->mailer = $mailer;
        $this->security = $security;
    }
    /*---- Début  "Page de rdv Admin" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/rdv/', name: 'admin_rdv')]
    public function rdv(): Response
    {

        return $this->render('rdv/listeRdv.html.twig', [
            'Rdvs' => $this->rdv->findBy(['rdv_valide' => false]),

        ]);
    }
    /*---- Fin "Page de rdv Admin" ---- */

    /*---- Début  "Page de validation rdv Admin " ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/rdv/validé/{id}', name: 'admin_rdv_valide')]
    public function valideRdv($id): Response
    {
        // On stock le rendez-vous selectionné dans une variable
        $rdv = $this->rdv->find($id);

        // On initialise les paramètre pour l'envois des mails
        $context = [
            'rdv' => $rdv
        ];
        
        if (!$this->rdv) {
            throw new NotFoundHttpException("Ce rendez-vous n'existe pas");
        }
        // On configure le booléen a true pour spécifier que le rendez-vous est valide et ainsi le rajouter dans l'historique des rendez vous
        $rdv->setRdvValide(true);
        $this->em->flush();
        // On envoie le mail au client
        $this->mailer->send(
            new Address('marie@calendrier-mmg.fr'),
            new Address($rdv->getClient()->getEmail(), $rdv->getClient()->getPrenom()),
            'Validation du Rendez-vous du ' . $rdv->getHoraire()->getHoraire()->format('d/m/Y  H:i '),
            'contactValid',
            $context
        );
        // On envoie le mail a l'admin
        $this->mailer->send(
            new Address('marie@calendrier-mmg.fr'),
            new Address('mariesix29@yahoo.fr'),
            'Validation du Rendez-vous avec ' . $rdv->getClient()->getPrenom() . ' ' . $rdv->getClient()->getNom() . ' du ' . $rdv->getHoraire()->getHoraire()->format('d/m/Y  H:i '),
            'contactValidAdmin',
            $context
        );
        $this->addFlash('message', 'Rendez-vous validé avec succès');
        return $this->redirectToRoute('admin_rdv');
    }
    /*---- Fin "Page de validation rdv " ---- */

    /*---- Début  "Page de l'historique Admin" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/historique', name: 'admin_historique')]
    public function historiqueRdv(): Response
    {

        return $this->render('rdv/historique.html.twig', [
            'Rdvs' => $this->rdv->findBy(['rdv_valide' => true]),

        ]);
    }
    /*---- Fin "Page de l'historique Admin" ---- */

    /*---- Debut "Page de l'historique User" ---- */
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/user/historique', name: 'client_Historique')]

    public function userHistorique(): Response
    {
        $client = $this->getUser();

        return $this->render('rdv/historique.html.twig', [
            'Rdvs' => $this->rdv->findby(['client' => $client, 'rdv_valide' => true])
        ]);
    }
    /*---- Fin "Page de l'historique User" ---- */
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/user/rdv', name: 'client_rdv')]

    public function userRdv(): Response
    {
        $client = $this->getUser();


        return $this->render('rdv/listeRdv.html.twig', [
            'Rdvs' => $this->rdv->findby(['client' => $client, 'rdv_valide' => false]),

        ]);
    }

    #[Route('/user/rdv/{id}', name: 'detailRdv', requirements: ["id" => "\d+"])]
    public function DetailRdvAdmin($id): Response
    {
        $rdv = $this->rdv->find($id);
        $this->denyAccessUnlessGranted('rdv_read', $rdv);

        if (!$rdv) {
            throw new NotFoundHttpException("Ce rendez-vous n'existe pas");
        }


        return $this->render('rdv/detailRdv.html.twig', [
            'rdv' => $rdv
        ]);
    }

    /**   
     * @IsGranted("ROLE_USER")
     */
    #[Route('/user/rdv/editHoraireRdv/{id}', name: 'editHoraireRdv', requirements: ["id" => "\d+"])]
    public function editRdvHoraireUser($id, Request $request, PlageHoraireRepository $plageHoraireRepository ): Response
    {
        // On stock le rendez-vous selectionné dans une variable
        $rdv = $this->rdv->find($id);

        if (!$rdv) {
            throw new NotFoundHttpException("Ce rendez-vous n'existe pas");
        }
        // Si ce n'est pas un admin ou le propriétaire du rendez-vous , il ne pourra pas modifier l'horaire
        $this->denyAccessUnlessGranted('rdv_edit', $rdv);

        // On stocke le l'horaire du rendez-vous séléctionné dans une variable
        $horaireActuelle = $rdv->getHoraire();

        // On stocke les horaire disponible dans une variable
        $HoraireDisponible =  $plageHoraireRepository->findDateAfterNow();

        // On crée le formulaire et on le stock dans une variable
        $form = $this->createForm(EditHoraireRdvType::class, $rdv);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // On rend l'ancienne horaire disponible donc visible pour reprendre un rendez-vous
            $horaireActuelle->setHorairePrise(false);
            //On rend l'horaire sélectionnée indisponible 
            $rdv->getHoraire()->setHorairePrise(true);
            $this->em->flush();
            
            
            
            $context = [
                'rdv' => $rdv
            ];

            $this->mailer->send(
                new Address('marie@calendrier-mmg.fr'),
                new Address($rdv->getClient()->getEmail(), $rdv->getClient()->getPrenom()),
                'Modification de votre rendez-vous',
                'contactEdit',
                $context
            );

            $this->mailer->send(
                new Address('marie@calendrier-mmg.fr'),
                new Address('mariesix29@yahoo.fr'),
                'Modification du rendez-vous avec ' . $rdv->getClient()->getPrenom() . ' ' . $rdv->getClient()->getNom(),
                'contactEditAdmin',
                $context
            );


            $this->addFlash('message', "L'horaire du rendez-vous vient d'être modifié!");
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_rdv');
            } else {
                return $this->redirectToRoute('client_rdv');
            }
        }
        return $this->render('rdv/editRdv.html.twig', [
            'form' => $form->createView(),
            'horaireDisponible' => $HoraireDisponible
        ]);
    }

    /*---- Fin "Modification d'un rdv " ---- */

    /*---- Début "Supprimer un rdv user" ---- */
    /**   
     * @IsGranted("ROLE_USER")
     */
    #[Route('user/rdv/removeRdv/{id}', name: 'removeRdv', requirements: ["id" => "\d+"])]
    public function RemoveRdvUser($id): Response
    {
        // On stocke le rendez-vous séléctionné dans une variable
        $rdv = $this->rdv->find($id);

        // Si ce n'est pas un admin ou le propriétaire du rendez-vous , il ne pourra pas supprimer le rendez-vous
        $this->denyAccessUnlessGranted('rdv_delete', $rdv);

        // On stocke l'horaire du rendez-vous seléctionné dans une variable
        $horaireActuelle = $rdv->getHoraire();
        if (!$rdv) {
            throw new NotFoundHttpException("Ce rendez-vous n'existe pas");
        }
        // On modifie l'horaire de ce rendez-vous pour le rendre disponible pour les autres utilisateurs
        $horaireActuelle->setHorairePrise(false);

        // On supprime les données du rendez-vous
        $this->em->remove($rdv);
        // On envois la suppression a la base de données
        $this->em->flush();

        // On configure les paramétre néccessaire pour les vues des emails
        $context = [
            'rdv' => $rdv
        ];
        // On envois un mail au client
        $this->mailer->send(
            new Address('marie@calendrier-mmg.fr'),
            new Address($rdv->getClient()->getEmail(), $rdv->getClient()->getPrenom()),
            'Annulation du rendez-vous du ' . $rdv->getHoraire()->getHoraire()->format('d/m/Y  H:i '),
            'contactDelete',
            $context
        );

        $this->mailer->send(
            new Address('marie@calendrier-mmg.fr'),
            new Address('mariesix29@yahoo.fr'),
            'Annulation du Rendez-vous avec ' . $rdv->getClient()->getPrenom() . ' ' . $rdv->getClient()->getNom() . ' du ' . $rdv->getHoraire()->getHoraire()->format('d/m/Y  H:i '),
            'contactDeleteAdmin',
            $context
        );

        $this->addFlash('message', 'Rendez-vous supprimée avec succès');

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_rdv');
        } else {
            return $this->redirectToRoute('client_rdv');
        }
    }
    /*---- Fin "Supprimer un rdv user" ---- */
}
