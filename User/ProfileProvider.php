<?php

namespace Bkstg\FOSUserBundle\User;

use Bkstg\CoreBundle\User\ProfileProviderInterface;
use Bkstg\FOSUserBundle\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;

class ProfileProvider implements ProfileProviderInterface
{
    private $em;
    private $cache = [];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function loadProfileByUsername(string $username)
    {
        if (isset($this->cache[$username])) {
            return $this->cache[$username];
        }

        $profile_repo = $this->em->getRepository(Profile::class);
        $this->cache[$username] = $profile_repo->findProfileByUsername($username);

        return $this->cache[$username];
    }
}
