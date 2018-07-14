<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgFOSUserBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\EventListener;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\FOSUserBundle\Entity\ProductionMembership;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class MembershipDeletion
{
    /**
     * Remove memberships when parent production is removed.
     *
     * @param LifecycleEventArgs $args The arguments for this event.
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        // only act on "Production" entities.
        if (!$object instanceof Production) {
            return;
        }

        // Find and remove all memberships for this group.
        $om = $args->getObjectManager();
        foreach ($om->getRepository(ProductionMembership::class)->findBy(['group' => $object]) as $membership) {
            $om->remove($membership);
        }
    }
}
