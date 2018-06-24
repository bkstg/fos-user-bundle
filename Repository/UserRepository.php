<?php

namespace Bkstg\FOSUserBundle\Repository;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\FOSUserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    public function findAllActiveQuery(bool $only_profile = false)
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

    public function findAllBlockedQuery(bool $only_profile = false)
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

    public function findUsersByGroupQuery(Production $production, bool $only_profile = false)
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->join('u.memberships', 'm')
            ->join('m.group', 'g')
            ->andWhere($qb->expr()->eq('g', ':group'))
            ->andWhere($qb->expr()->eq('u.enabled', ':enabled'))
            ->andWhere($qb->expr()->eq('m.status', ':enabled'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('m.expiry'),
                $qb->expr()->gt('m.expiry', ':now')
            ))
            ->setParameter('group', $production)
            ->setParameter('enabled', true)
            ->setParameter('now', new \DateTime())
        ;

        if ($only_profile) {
            $qb->andWhere($qb->expr()->eq('u.has_profile', ':has_profile'))
                ->setParameter('has_profile', true);
        }

        return $qb->getQuery();
    }

    public function findAllActive(bool $only_profile = false)
    {
        return $this->findAllActiveQuery($only_profile)->getResult();
    }

    public function findAllBlocked(bool $only_profile = false)
    {
        return $this->findAllBlockedQuery($only_profile)->getResult();
    }

    public function findUsersByGroup(Production $production, bool $only_profile = false)
    {
        return $this->findUsersByGroupQuery($production, $only_profile)->getResult();
    }
}
