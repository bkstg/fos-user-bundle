<?php

namespace Bkstg\FOSUserBundle\Security;

use Bkstg\FOSUserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SuperAdminVoter extends Voter
{
    /**
     * {@inheritdoc}
     *
     * @param  mixed $attribute The attribute to vote on.
     * @param  mixed $subject   The subject to vote on.
     * @return boolean
     */
    protected function supports($attribute, $subject): bool
    {
        return true;
    }

    /**
     * This voter allows users with the super admin role access to all ops.
     *
     * @param mixed          $attribute The attribute to vote on.
     * @param mixed          $subject   The subject to vote on.
     * @param TokenInterface $token     The token to vote using.
     * @return boolean
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // Get the current user from the token.
        $user = $token->getUser();

        // If this user has the super admin role always allow all operations.
        if ($user instanceof User && $user->hasRole('ROLE_SUPER_ADMIN')) {
            return true;
        }

        return false;
    }
}
