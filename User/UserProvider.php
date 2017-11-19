<?php

namespace Bkstg\FOSUserBundle\User;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\CoreBundle\User\UserProviderInterface;
use Bkstg\FOSUserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use MidnightLuke\GroupSecurityBundle\Model\GroupInterface;

class UserProvider implements UserProviderInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function loadUserByUsername(string $username)
    {
        $user_repo = $this->em->getRepository(User::class);
        return $user_repo->findOneBy(['username' => $username]);
    }
}
