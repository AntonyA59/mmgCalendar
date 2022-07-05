<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Form\Inscription\ClientsType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * Classe qui permet aux utilisateurs de s'inscrire
 */
class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'inscription')]
    // On injecte nos services qu'on a besoin
    public function inscription(Request $request, 
    EntityManagerInterface $manager, 
    UserPasswordHasherInterface $encoder,
    GuardAuthenticatorHandler $guardHandler, 
    LoginFormAuthenticator $authenticator ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        // Permet d'instancier un nouveau client dans la table Clients
        $clients = new Clients();

        // Permet de générer le formulaire "Clientstypes"
        $form_clients = $this->createForm(ClientsType::class, $clients);

        // La méthode handleRequest permet détecter quand le formulaire a été soumis
        $form_clients->handleRequest($request);
        
        //Si le formulaire est soumis ET validé
        if($form_clients->isSubmitted() && $form_clients->isValid()) {
            //La méthode "hashPassword" permet de hashé le mot de passe que le client a soumis dans "mot de passe" du formulaire.
            $hash_password = $encoder->hashPassword($clients, $clients->getPassword());
            $clients->setPassword($hash_password);
            $clients->setRoles(['ROLE_USER']);

            // On fait le lien entre Doctrine et l'objet avec l'entityManager
            $manager->persist($clients);

            // On ajoute les données du client dans la base de donnée  
            $manager->flush();
            // On ajoute un message lorsque l'inscription est réussi
            $this->addFlash('message', "Votre compte a bien été créé!");
            
            // La méthode "authenticateUserAndHandleSuccess" permet a l'utilisateur d'être automatiquement connecté aprés être inscrit
            return $guardHandler->authenticateUserAndHandleSuccess(
                $clients,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }
        return $this->render('security/inscription.html.twig', [
            //on ajoute les paramétre du formulaire dans la vue
            'formulaire' => $form_clients->createView(),
        ]);
    }
}