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

class UserRepository extends EntityRepository
{
    /**
     * Prepare query to find all active users.
     *
     * @param bool $only_profile Optionally filter by only users with a profile.
     *
     * @return Query
     */
    public function findAllActiveQuery(bool $only_profile = false): Query
    {
        $qb = $this->createQueryBuilder('u');
        $qb->andWhere($qb->expr()->eq('u.enabled', ':enabled'))
            ->setParameter('enabled', true);

        if ($only_profile) {
            $qb->andWhere($qb->expr()->eq('u.has_profile', ':has_profile'))
                ->setParameter('has_profile', true);
        }

        return $qb->getQuery();
    }

    /**
     * Prepare query to find all blocked users.
     *
     * @param bool $only_profile Optionally filter by only users with a profile.
     *
     * @return Query
     */
    public function findAllBlockedQuery(bool $only_profile = false): Query
    {
        $qb = $this->createQueryBuilder('u');
        $qb->andWhere($qb->expr()->eq('u.enabled', ':enabled'))
            ->setParameter('enabled', false);

        if ($only_profile) {
            $qb->andWhere($qb->expr()->eq('u.has_profile', ':has_profile'))
                ->setParameter('has_profile', true);
        }

        return $qb->getQuery();
    }

    /**
     * Prepare query to find all users by production.
     *
     * @param Production $production   The production to search for.
     * @param bool       $only_profile Optionally filter by only users with a profile.
     *
     * @return Query
     */
    public function findUsersByGroupQuery(Production $production, bool $only_profile = false): Query
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->join('u.memberships', 'm')
            ->join('m.group', 'g')
            ->andWhere($qb->expr()->eq('g', ':group'))
            ->andWhere($qb->expr()->eq('u.enabled', ':enabled'))
            ->andWhere($qb->expr()->eq('m.active', ':active'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('m.expiry'),
                $qb->expr()->gt('m.expiry', ':now')
            ))
            ->setParameter('group', $production)
            ->setParameter('enabled', true)
            ->setParameter('active', true)
            ->setParameter('now', new \DateTime())
        ;

        if ($only_profile) {
            $qb->andWhere($qb->expr()->eq('u.has_profile', ':has_profile'))
                ->setParameter('has_profile', true);
        }

        return $qb->getQuery();
    }

    /**
     * Find all active users.
     *
     * @param bool $only_profile Optionally filter by only users with a profile.
     *
     * @return User[]
     */
    public function findAllActive(bool $only_profile = false)
    {
        return $this->findAllActiveQuery($only_profile)->getResult();
    }

    /**
     * Find all blocked users.
     *
     * @param bool $only_profile Optionally filter by only users with a profile.
     *
     * @return User[]
     */
    public function findAllBlocked(bool $only_profile = false)
    {
        return $this->findAllBlockedQuery($only_profile)->getResult();
    }

    /**
     * Find all users by production.
     *
     * @param Production $production   The production to search for.
     * @param bool       $only_profile Optionally filter by only users with a profile.
     *
     * @return User[]
     */
    public function findUsersByGroup(Production $production, bool $only_profile = false)
    {
        return $this->findUsersByGroupQuery($production, $only_profile)->getResult();
    }
}
