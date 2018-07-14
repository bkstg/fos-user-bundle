<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgFOSUserBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\User;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\CoreBundle\User\MembershipProviderInterface;
use Bkstg\CoreBundle\User\ProductionMembershipInterface;
use Bkstg\CoreBundle\User\UserInterface;
use Bkstg\FOSUserBundle\Entity\ProductionMembership;
use Doctrine\ORM\EntityManagerInterface;

class MembershipProvider implements MembershipProviderInterface
{
    private $em;
    private $repo;

    /**
     * Create a new membership provider service.
     *
     * @param EntityManagerInterface $em The entity manager service.
     */
    public function __construct(EntityManagerInterface $em)
    {
        // Create the repo for use in service.
        $this->em = $em;
        $this->repo = $this->em->getRepository(ProductionMembership::class);
    }

    /**
     * {@inheritdoc}
     *
     * @param Production    $production The production to load a membership for.
     * @param UserInterface $user       The user to load a membership for.
     *
     * @return ?ProductionMembershipInterface
     */
    public function loadMembership(Production $production, UserInterface $user): ?ProductionMembershipInterface
    {
        return $this->repo->findOneBy(['member' => $user, 'group' => $production]);
    }

    /**
     * {@inheritdoc}
     *
     * @param Production $production The production to load memberships for.
     *
     * @return ProductionMembershipInterface[]
     */
    public function loadActiveMembershipsByProduction(Production $production)
    {
        return $this->repo->findAllActive($production);
    }

    /**
     * {@inheritdoc}
     *
     * @param UserInterface $user The user to load active memberships for.
     *
     * @return ProductionMembershipInterface[]
     */
    public function loadActiveMembershipsByUser(UserInterface $user)
    {
        return $this->repo->findActiveMemberships($user);
    }

    /**
     * {@inheritdoc}
     *
     * @param Production $production The production to load memberships for.
     *
     * @return ProductionMembershipInterface[]
     */
    public function loadAllMembershipsByProduction(Production $production)
    {
        return $this->repo->findBy(['group' => $production]);
    }

    /**
     * {@inheritdoc}
     *
     * @param UserInterface $user The user to load memberships for.
     *
     * @return ProductionMembershipInterface[]
     */
    public function loadAllMembershipsByUser(UserInterface $user)
    {
        return $this->repo->findBy(['member' => $user]);
    }
}
