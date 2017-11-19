<?php

namespace Bkstg\FOSUserBundle\User;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\CoreBundle\User\MembershipProviderInterface;
use Bkstg\FOSUserBundle\Entity\ProductionMembership;
use Doctrine\ORM\EntityManagerInterface;
use MidnightLuke\GroupSecurityBundle\Model\GroupInterface;

class MembershipProvider implements MembershipProviderInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMembershipsByGroup(GroupInterface $group)
    {
        if (!$group instanceof Production) {
            return [];
        }

        $membership_repo = $this->em->getRepository(ProductionMembership::class);
        return $membership_repo->findMembershipsByGroup($group);
    }
}
