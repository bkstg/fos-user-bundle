<?php

namespace Bkstg\FOSUserBundle\EventListener;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\FOSUserBundle\Entity\ProductionMembership;
use Bkstg\FOSUserBundle\Entity\User;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use MidnightLuke\GroupSecurityBundle\Model\GroupMembershipInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MembershipCreator
{
    private $token_storage;
    private $session;
    private $translator;

    public function __construct(
        TokenStorageInterface $token_storage,
        SessionInterface $session,
        TranslatorInterface $translator
    ) {
        $this->token_storage = $token_storage;
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * When a user creates a production make them an admin in the production.
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $user = $this->token_storage->getToken()->getUser();

        // Only act on "Production" entities.
        if (!$object instanceof Production
          || !$user instanceof User) {
            return;
        }

        $om = $args->getObjectManager();

        // Create and persist new membership.
        $membership = new ProductionMembership();
        $membership->setGroup($object);
        $membership->setMember($user);
        $membership->setStatus(GroupMembershipInterface::STATUS_ACTIVE);
        $membership->addRole('GROUP_ROLE_ADMIN');
        $om->persist($membership);

        // Set message.
        $this->session->getFlashBag()->add(
            'success',
            $this->translator->trans('You have been made a member of "%production%".', [
                '%production%' => $object->getName(),
            ])
        );
    }
}
