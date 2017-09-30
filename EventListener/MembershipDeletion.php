<?php

namespace Bkstg\FOSUserBundle\EventListener;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\FOSUserBundle\Entity\ProductionMembership;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class MembershipDeletion
{
    /**
     * Removes memberships when parent production is removed.
     *
     * Since productions are not aware of their memberships this  is the
     * responsibility of the user bundle.
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        // only act on "Production" entities.
        if (!$object instanceof Production) {
            return;
        }

        $om = $args->getObjectManager();
        foreach ($om->getRepository(ProductionMembership::class)->findBy(['group' => $object]) as $membership) {
            $om->remove($membership);
        }
    }
}
