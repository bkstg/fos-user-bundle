<?php

namespace Bkstg\FOSUserBundle\User;

use Bkstg\CoreBundle\User\ProfileProviderInterface;
use Bkstg\FOSUserBundle\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;

class ProfileProvider implements ProfileProviderInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function loadProfileByUsername(string $username)
    {
        $profile_repo = $this->em->getRepository(Profile::class);
        return $profile_repo->findProfileByUsername($username);
    }
}
