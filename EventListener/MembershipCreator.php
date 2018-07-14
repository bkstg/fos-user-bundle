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
use Bkstg\FOSUserBundle\Entity\User;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MembershipCreator
{
    private $token_storage;
    private $session;
    private $translator;

    /**
     * Create a new membership creator.
     *
     * @param TokenStorageInterface $token_storage The token storage service.
     * @param SessionInterface      $session       The session service.
     * @param TranslatorInterface   $translator    The translator service.
     */
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
     *
     * @param LifecycleEventArgs $args The event arguments.
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        // Ensure we only act on productions and that we have a user.
        $object = $args->getObject();
        $token = $this->token_storage->getToken();
        if (!$object instanceof Production
          || null === $token) {
            return;
        }

        // Ensure the user is one of our users.
        $user = $token->getUser();
        if (!$user instanceof User) {
            return;
        }

        // Get the object manager for this event.
        $om = $args->getObjectManager();

        // Create and persist new membership.
        $membership = new ProductionMembership();
        $membership->setGroup($object);
        $membership->setMember($user);
        $membership->setActive(true);
        $membership->addRole('GROUP_ROLE_ADMIN');
        $om->persist($membership);

        // Set message.
        $this->session->getFlashBag()->add(
            'success',
            $this->translator->trans('membership.user_added', [
                '%production%' => $object->getName(),
            ])
        );
    }
}
