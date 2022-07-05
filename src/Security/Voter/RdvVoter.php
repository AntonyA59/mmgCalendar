<?php

namespace App\Security\Voter;

use App\Entity\Rdv;
use App\Entity\Clients;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class RdvVoter extends Voter
{
    const RDV_EDIT = 'rdv_edit';
    const RDV_DELETE = 'rdv_delete';
    const RDV_READ = 'rdv_read';
    private $security;
    
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    protected function supports(string $attribute, $rdv): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::RDV_EDIT, self::RDV_DELETE, self::RDV_READ]) 
        && $rdv instanceof \App\Entity\Rdv;
    }

    protected function voteOnAttribute(string $attribute, $rdv, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        //On vérifie si le user est admin
        if($this->security->isGranted('ROLE_ADMIN')) return true;
        
        //On vérifie si le rdv a un propriétaitre
        if (null === $rdv->getClient()) {
            return false;
        }
        //On verifie si le rdv est valide
        if (true === $rdv->getRdvValide()){
            return false;
        }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::RDV_READ:
                // On vérifie si on peut lire
                return $this->canRead($rdv, $user);
                break;
            case self::RDV_EDIT:
                // On vérifie si on peut editer
                return $this->canEdit($rdv, $user);
                break;
            case self::RDV_DELETE:
                // On vérifie si on peut supprimer
                return $this->canDelete($rdv, $user);

        }

        return false;
    }

    private function canEdit(Rdv $rdv, Clients $clients)
    {
        // le propriétaitre du rdv peut le modifier
        return $clients === $rdv->getClient();
    }

    private function canDelete(Rdv $rdv, Clients $clients)
    {
        // le propriétaitre du rdv peut le supprimer 
        return $clients === $rdv->getClient();
    }
    private function canRead(Rdv $rdv, Clients $clients)
    {
        // le propriétaitre du rdv peut lire
        return $clients === $rdv->getClient();
    }
}
