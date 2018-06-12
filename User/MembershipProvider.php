<?php

namespace Bkstg\FOSUserBundle\User;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\CoreBundle\User\MembershipProviderInterface;
use Bkstg\CoreBundle\User\ProductionMembershipInterface;
use Bkstg\CoreBundle\User\UserInterface;
use Bkstg\FOSUserBundle\Entity\ProductionMembership;
use Doctrine\ORM\EntityManagerInterface;
use MidnightLuke\GroupSecurityBundle\Model\GroupInterface;

class MembershipProvider implements MembershipProviderInterface
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $this->em->getRepository(ProductionMembership::class);
    }

    /**
     * {@inheritdoc}
     */
    public function loadMembership(Production $production, UserInterface $user): ?ProductionMembershipInterface
    {
        return $this->repo->findOneBy(['member' => $user, 'group' => $production]);
    }

    /**
     * {@inheritdoc}
     */
    public function loadActiveMembershipsByProduction(Production $production)
    {
        return $this->repo->findAllActive($production);
    }

    /**
     * {@inheritdoc}
     */
    public function loadActiveMembershipsByUser(UserInterface $user)
    {
        return $this->repo->findActiveMemberships($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadAllMembershipsByProduction(Production $production)
    {
        return $this->repo->findBy(['group' => $production]);
    }

    /**
     * {@inheritdoc}
     */
    public function loadAllMembershipsByUser(UserInterface $user)
    {
        return $this->repo->findBy(['member' => $user]);
    }
}
