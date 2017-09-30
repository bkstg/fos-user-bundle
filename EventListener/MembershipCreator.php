<?php

namespace Bkstg\FOSUserBundle\EventListener;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\FOSUserBundle\Entity\ProductionMembership;
use Bkstg\FOSUserBundle\Entity\User;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use MidnightLuke\GroupSecurityBundle\Model\GroupMembershipInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MembershipCreator
{
    private $token_storage;
    public function __construct(TokenStorageInterface $token_storage) {
        $this->token_storage = $token_storage;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $user = $this->token_storage->getToken()->getUser();

        // only act on some "Product" entity
        if (!$object instanceof Production
          || !$user instanceof User) {
            return;
        }

        $om = $args->getObjectManager();
        $membership = new ProductionMembership();
        $membership->setGroup($object);
        $membership->setMember($user);
        $membership->setStatus(GroupMembershipInterface::STATUS_ACTIVE);
        $membership->addRole('GROUP_ROLE_ADMIN');
        $om->persist($membership);
    }
}
