<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
// use Symfony\Component\DependencyInjection\Loader\Configurator\security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class LandTitleVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\LandTitle;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $landTitle = $subject;

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }


        // ROLE_CTI a tous les droits
        if ($this->security->isGranted('ROLE_CTI')) {
            return true;
        }

        // ROLE_CADASTRE peut voir tous les documents mais pas modifier
        if ($attribute === self::VIEW && $this->security->isGranted('ROLE_CADASTRE')) {
            return true;
        }

        // ROLE_OWNER ne peut voir que ses propres documents
        if ($attribute === self::VIEW && $user === $landTitle->getOwner()) {
            return true;
        }

        // // ... (check conditions and return true to grant permission) ...
        // switch ($attribute) {
        //     case self::EDIT:
        //         // logic to determine if the user can EDIT
        //         // return true or false
        //         break;

        //     case self::VIEW:
        //         // logic to determine if the user can VIEW
        //         // return true or false
        //         break;
        // }

        return false;
    }
}
