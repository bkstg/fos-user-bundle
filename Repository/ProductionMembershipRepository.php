<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgCoreBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\Repository;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\FOSUserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ProductionMembershipRepository extends EntityRepository
{
    /**
     * Find active memberships for a given user.
     *
     * @param User $user The user to search for.
     *
     * @return ProductionMembership[]
     */
    public function findActiveMemberships(User $user)
    {
        return $this->findActiveMembershipsQuery($user)->getResult();
    }

    /**
     * Prepare query for finding active memberships by user.
     *
     * @param User $user The user to search for.
     *
     * @return Query
     */
    public function findActiveMembershipsQuery(User $user): Query
    {
        $qb = $this->createQueryBuilder('m');

        return $qb
            ->join('m.group', 'g')

            // Add conditions.
            ->andWhere($qb->expr()->eq('m.member', ':member'))
            ->andWhere($qb->expr()->eq('m.status', ':membership_status'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('m.expiry'),
                $qb->expr()->gt('m.expiry', ':now')
            ))
            ->andWhere($qb->expr()->eq('g.status', ':production_status'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('g.expiry'),
                $qb->expr()->gt('g.expiry', ':now')
            ))

            // Add parameters.
            ->setParameter('member', $user)
            ->setParameter('membership_status', true)
            ->setParameter('production_status', true)
            ->setParameter('now', new \DateTime())

            // Order by and get results.
            ->orderBy('g.name')
            ->getQuery();
    }

    /**
     * Find active memberships for a production.
     *
     * @param Production $production The production to search for.
     *
     * @return ProductionMembership[]
     */
    public function findAllActive(Production $production)
    {
        return $this->findAllActiveQuery($production)->getResult();
    }

    /**
     * Prepare query for finding active memberships for a production.
     *
     * @param Production $production The production to search for.
     *
     * @return Query
     */
    public function findAllActiveQuery(Production $production): Query
    {
        $qb = $this->createQueryBuilder('m');

        return $qb
            // Add conditions.
            ->andWhere($qb->expr()->eq('m.status', ':membership_status'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('m.expiry'),
                $qb->expr()->gt('m.expiry', ':now')
            ))
            ->andWhere($qb->expr()->eq('m.group', ':production'))

            // Add parameters.
            ->setParameter('membership_status', true)
            ->setParameter('production', $production)
            ->setParameter('now', new \DateTime())

            // Order by and get results.
            ->getQuery();
    }

    /**
     * Find inactive memberships for a production.
     *
     * @param Production $production The production to search for.
     *
     * @return ProductionMembership[]
     */
    public function findAllInactive(Production $production)
    {
        return $this->findAllInactiveQuery($production)->getResult();
    }

    /**
     * Prepare query for finding inactive memberships for a production.
     *
     * @param Production $production The production to search for.
     *
     * @return Query
     */
    public function findAllInactiveQuery(Production $production): Query
    {
        $qb = $this->createQueryBuilder('m');

        return $qb
            // Add conditions.
            ->andWhere($qb->expr()->eq('m.status', ':membership_status'))
            ->orWhere($qb->expr()->andX(
                $qb->expr()->isNotNull('m.expiry'),
                $qb->expr()->lt('m.expiry', ':now')
            ))
            ->andWhere($qb->expr()->eq('m.group', ':production'))

            // Add parameters.
            ->setParameter('membership_status', false)
            ->setParameter('production', $production)
            ->setParameter('now', new \DateTime())

            // Order by and get results.
            ->getQuery();
    }

    /**
     * Find all memberships for a production.
     *
     * @param Production $production The production to search for.
     *
     * @return ProductionMembership[]
     */
    public function findMembershipsByGroup(Production $production)
    {
        return $this->findMembershipsByGroupQuery($production)->getResult();
    }

    /**
     * Prepare the query for searching for memberships for a production.
     *
     * @param Production $production The production to search for.
     *
     * @return Query
     */
    public function findMembershipsByGroupQuery(Production $production): Query
    {
        $qb = $this->createQueryBuilder('m');

        return $qb
            ->addSelect('u')
            ->join('m.group', 'g')
            ->join('m.member', 'u')
            ->andWhere($qb->expr()->eq('g', ':group'))
            ->setParameter('group', $production)
            ->getQuery();
    }
}
