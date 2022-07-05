<?php

namespace App\Controller;

use App\Entity\Rdv;
use App\Entity\Clients;
use App\Form\Rdv\RdvAdminType;
use App\Form\Rdv\RdvType;
use App\Repository\PlageHoraireRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class PriseRdvController extends AbstractController
{
    protected $requestStack;
    protected $em;
    protected $mailer;
    protected $plageHoraireRepository;

    public function __construct(RequestStack $requestStack,EntityManagerInterface $em,SendMailService $mailer,PlageHoraireRepository $plageHoraireRepository)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->mailer = $mailer;
        $this->plageHoraireRepository = $plageHoraireRepository;
    }

    #[Route('/priseRdv', name: 'prise_rdv')]
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        
        return $this->render('rdv/rdv.html.twig', []);
    }

    /** 
     * Fonction permettant à l'utilisateur de prendre un rendez-vous
     *   
     * @IsGranted("ROLE_USER")
     */
    #[Route('/user/priseRdv', name: 'prise_rdv_user')]
    public function priseRdv(): Response
    {
        /* On définit le querrybuilder dans une variable qui permet selectionner toutes les horaires aprés 12h par rapport a la date actuelle */
        $rdvDisponible = $this->plageHoraireRepository->findDateAfterNow();
        
        /* On définit l'utilisateur actuellement connecté dans une variable $client en récupérant toutes les informations de celui-ci */
        $clients = $this->getUser();
        
        /* On instancie une nouveau rendez-vous dans la table RDV */
        $rdv = new Rdv;

        /* On crée le formulaire RdvType et on le stocke dans une variable */
        $formRdv = $this->createForm(RdvType::class, $rdv);
        $contact = $formRdv->handleRequest($this->requestStack->getCurrentRequest());

        if ($formRdv->isSubmitted() && $formRdv->isValid()) {
            /* On reprend l'id du client actuellement connecté */
            $rdv->setClient($clients);
            /* On sélectionne l'horaire en configurant la colonne HorairePrise a true , il ne sera plus visible par les autres utilisateurs */
            $rdv->getHoraire()->setHorairePrise(true);

            $this->em->persist($rdv);
            $this->em->flush();
            /* On configure les paramétre néccessaire pour la vue des emails envoyés */
            $context = [
                'message' => $contact->get('description')->getData(),
                'rdv' => $rdv
            ];
            /* On envoi le mail a l'utilisateur actuellement connecté */
            $this->mailer->send(
                new Address('marie@calendrier-mmg.fr'),
                new Address($clients->getEmail(), $clients->getPrenom()),
                'Votre rendez-vous a bien été enregistré le '.$rdv->getHoraire()->getHoraire()->format('d/m/Y  H:i '),
                'contact',
                $context
            );
            /* On envoie le mail a l'admin */
            $this->mailer->send(
                new Address('marie@calendrier-mmg.fr'),
                new Address('mariesix29@yahoo.fr'),
                'un rdv a été planifié le '.$rdv->getHoraire()->getHoraire()->format('d/m/Y  H:i '),
                'contactAdmin',
                $context
            );



            $this->addFlash('message', "Votre Rendez-Vous a bien été confirmé !");

            return $this->redirectToRoute('home');
        }

        return $this->render('rdv/priseDeRdv.html.twig', [
            'FormRdv' => $formRdv->createView(),
            'horaireDispo' => $rdvDisponible
        ]);
    }

    /**   
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/priseRdv', name: 'prise_rdv_admin')]
    public function priseRdvAdmin(): Response
    {

        $rdvDisponible = $this->plageHoraireRepository->findDateAfterNow();
        $rdv = new Rdv;
        $form = $this->createForm(RdvAdminType::class, $rdv);
        $contact = $form->handleRequest($this->requestStack->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $rdv->getHoraire()->setHorairePrise(true);
            $this->em->persist($rdv);
            $this->em->flush();
            $context = [
                'message' => $contact->get('description')->getData(),
                'rdv' => $rdv
            ];
            $this->mailer->send(
                new Address('marie@calendrier-mmg.fr'),
                new Address($rdv->getClient()->getEmail(), $rdv->getClient()->getPrenom()),
                'Votre rendez-vous a bien etait enregistré',
                'contact',
                $context
            );
            $this->mailer->send(
                new Address('marie@calendrier-mmg.fr'),
                new Address('mariesix29@yahoo.fr'),
                'un rdv a été planifié le '.$rdv->getHoraire()->getHoraire()->format('d/m/Y  H:i '),
                'contactAdmin',
                $context
            );
            $this->addFlash('message', "Votre Rendez-Vous a bien été confirmé !");

            return $this->redirectToRoute('home');
        }

        return $this->render('rdv/admin/priseDeRdvAdmin.html.twig', [
            'form' => $form->createView(),
            'horaireDispo' => $rdvDisponible
        ]);
    }
}
