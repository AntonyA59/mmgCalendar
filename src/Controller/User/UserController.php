<?php

namespace App\Controller\User;


use App\Entity\Clients;
use App\Form\Dashboard\EditPassType;
use App\Form\Dashboard\EditProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    protected $requestStack;
    protected $em;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $em
        )
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
        
    }
    /* ----- DEBUT DASHBOARD USER ----- */
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/user/dashboard', name: 'dashboard_client')]

    public function dashBoardClient(): Response
    {
        return $this->render('dashboard/user/DashboardClient.html.twig');
    }
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/user/dashboard/edit', name: 'dashboard_client_edit')]

    public function editProfil(): Response
    {
        $client = $this->getUser();
        $formEdit = $this->createForm(EditProfilType::class, $client);

        $formEdit->handleRequest($this->requestStack->getCurrentRequest());

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            $this->em->flush();

            $this->addFlash('message', 'Profil mis à jour');
            return $this->redirectToRoute('dashboard_client');
        }

        return $this->render('dashboard/EditProfil.html.twig', [
            'formEdit' => $formEdit->createView(),
        ]);
    }
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/user/dashboard/editPass', name: 'dashboard_client_pass')]

    public function editPass(UserPasswordHasherInterface $encoder): Response
    {

        $client = $this->getUser();
        $formEditPass = $this->createForm(EditPassType::class, $client);
        $formEditPass->handleRequest($this->requestStack->getCurrentRequest());

        if ($formEditPass->isSubmitted() && $formEditPass->isValid()) {
            $hash_password = $encoder->hashPassword($client, $client->getPassword());
            $client->setPassword($hash_password);
            $this->em->flush();

            $this->addFlash('message', 'Mot de passe mis à jour');
            return $this->redirectToRoute('dashboard_client');
        }

        return $this->render('dashboard/EditPass.html.twig', [
            'formEditPass' => $formEditPass->createView(),
        ]);
    }
    #[Route('/user/dashboard/disable', name: 'dashboard_client_disable')]

    public function disable(): Response
    {
        /** @var Clients */
        $client = $this->getUser();
        $client->setRoles(['ROLE_DISABLE']);
        
        $this->em->flush();

        $this->addFlash('alert', 'Votre compte a été desactivé avec succés');

        return $this->redirectToRoute('app_logout');
    }
    #[Route('/active', name: 'active')]

    public function active(): Response
    {  
        /** @var Clients */
        $client = $this->getUser();

        $client->setRoles(['ROLE_USER']);
        $this->em->flush();

        $this->addFlash('message', 'Votre compte à été réactiver avec succès, vous pouvez vous reconnecter en toute sécurité');
        return $this->redirectToRoute('app_login');


    }
    #[Route('/active/menu', name: 'active_menu')]

    public function activeMenu(): Response
    {  
        return $this->render('main/active.html.twig');
    }

}
