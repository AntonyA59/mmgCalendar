<?php

namespace App\Controller\Admin;

use App\Entity\Clients;
use App\Form\Dashboard\EditProfilType;
use App\Form\Inscription\ClientsType;
use App\Repository\ClientsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;



class ClientAdminController extends AbstractController
{

    protected $em;
    protected $clientsRepository;
    protected $requestStack;


    public function __construct(
        EntityManagerInterface $em, 
        ClientsRepository $clientsRepository, 
        RequestStack $requestStack)
    {
        $this->em = $em;
        $this->clientsRepository = $clientsRepository;
        $this->requestStack = $requestStack;

    }
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/Client', name: 'admin_client')]
    public function pageClient(): Response
    {
        return $this->render('PageClientAdmin/PageClientAdmin.html.twig', [
            'Clients' => $this->clientsRepository->findUsersByRole($role = 'ROLE_USER'),
            'ClientsOff' => $this->clientsRepository->findUsersByRole($role = 'ROLE_DISABLE')

        ]);
    }

    /*---- Début "Ajout d'un client" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/client/addClient', name: 'admin_client_addClient')]
    public function ajoutClient(UserPasswordHasherInterface $encoder): Response
    {
        $clients = new Clients();

        $form_clients = $this->createForm(ClientsType::class, $clients);

        $form_clients->handleRequest($this->requestStack->getCurrentRequest());
        
        if ($form_clients->isSubmitted() && $form_clients->isValid()) {

            $hash_password = $encoder->hashPassword($clients, $clients->getPassword());
            $clients->setPassword($hash_password);
            $clients->setRoles(['ROLE_USER']);
            $this->em->persist($clients);
            $this->em->flush();

            $this->addFlash('message', "Votre client a bien été créé!");

            return $this->redirectToRoute('admin_client');
        }
        return $this->render('security/inscription.html.twig', [
            'formulaire' => $form_clients->createView(),
        ]);
    }
    /*---- Fin "Ajout d'un client" ---- */

    /*---- Début "Modification du profil d'un client" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/client/editClient/{id}', name: 'admin_client_editClient', requirements:["id"=>"\d+"])]
    public function editClientAdmin($id): Response
    {
        $client = $this->clientsRepository->find($id);
        if (!$client) {
            throw new NotFoundHttpException("Ce client n'existe pas");
        }
        $form_client = $this->createForm(EditProfilType::class,  $client);
        $form_client->handleRequest($this->requestStack->getCurrentRequest());
        if ($form_client->isSubmitted() && $form_client->isValid()) {
            $this->em->flush();

            $this->addFlash('message', "Les information de votre client a bien été modifié!");

            return $this->redirectToRoute('admin_client');
        }
        return $this->render('dashboard/EditProfil.html.twig', [
            'formEdit' => $form_client->createView(),
        ]);
    }
    /*---- Fin "Modification du profil d'un client" ---- */

    /*---- Début "Desactiver un client" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/client/desactiveClient/{id}', name: 'admin_client_desactiveClient',requirements:["id"=>"\d+"]), ]
    public function RemoveClientAdmin($id): Response
    {
        $client = $this->clientsRepository->find($id);
        if (!$client) {
            throw new NotFoundHttpException("Ce client n'existe pas");
        }
        $client->setRoles(['ROLE_DISABLE']);
        $this->em->flush();
        $this->addFlash('message', 'Client désactiver avec succès');

        return $this->redirectToRoute('admin_client');
    }
    /*---- Fin "Desactiver un client" ---- */
    /*---- Début "Réactiver un client" ---- */
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/client/activeClient/{id}', name: 'admin_client_activeClient', requirements:["id"=>"\d+"])]
    public function ActiveClientAdmin($id): Response
    {
        $client = $this->clientsRepository->find($id);
        if (!$client) {
            throw new NotFoundHttpException("Ce client n'existe pas");
        }
        $client->setRoles(['ROLE_USER']);
        $this->em->flush();
        $this->addFlash('message', 'Client réactiver avec succès');

        return $this->redirectToRoute('admin_client');
    }
    /*---- Fin "Réactiver un client" ---- */
}
