<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgFOSUserBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\Timeline\EventListener;

use Bkstg\FOSUserBundle\Entity\ProductionMembership;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Spy\Timeline\Driver\ActionManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class MembershipTimelineListener
{
    private $action_manager;
    private $user_provider;
    private $token_storage;

    /**
     * Create a new membership timeline listener.
     *
     * @param ActionManagerInterface $action_manager The action manager service.
     * @param UserProviderInterface  $user_provider  The user provider service.
     * @param TokenStorageInterface  $token_storage  The token storage service.
     */
    public function __construct(
        ActionManagerInterface $action_manager,
        UserProviderInterface $user_provider,
        TokenStorageInterface $token_storage
    ) {
        $this->action_manager = $action_manager;
        $this->user_provider = $user_provider;
        $this->token_storage = $token_storage;
    }

    /**
     * Create a new timeline entry when a memberhip is persisted.
     *
     * @param LifecycleEventArgs $args The lifecycle even args.
     *
     * @return void
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        // Get the membership, if not ours we leave.
        $membership = $args->getObject();
        if (!$membership instanceof ProductionMembership) {
            return;
        }

        // If there is no active token jump out.
        if (null === $token = $this->token_storage->getToken()) {
            return;
        }

        // Get the active user and membership components.
        $creator_component = $this->action_manager->findOrCreateComponent($token->getUser());
        $member_component = $this->action_manager->findOrCreateComponent($membership->getMember());
        $group_component = $this->action_manager->findOrCreateComponent($membership->getGroup());

        // Create the action and link it.
        $action = $this->action_manager->create($creator_component, 'added_member', [
            'directComplement' => $member_component,
            'indirectComplement' => $group_component,
        ]);

        // Update the action.
        $this->action_manager->updateAction($action);
    }
}
