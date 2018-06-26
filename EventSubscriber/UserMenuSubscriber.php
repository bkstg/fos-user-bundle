<?php

namespace Bkstg\FOSUserBundle\EventSubscriber;

use Bkstg\CoreBundle\Event\MenuCollectionEvent;
use Bkstg\CoreBundle\Event\UserMenuCollectionEvent;
use Bkstg\FOSUserBundle\BkstgFOSUserBundle;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserMenuSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $token_storage;

    /**
     * Create a new user menu subscriber.
     *
     * @param FactoryInterface      $factory       The menu factory service.
     * @param TokenStorageInterface $token_storage The token storage service.
     */
    public function __construct(
        FactoryInterface $factory,
        TokenStorageInterface $token_storage
    ) {
        $this->factory = $factory;
        $this->token_storage = $token_storage;
    }

    /**
     * Return the subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
           UserMenuCollectionEvent::NAME => [
               ['addProfileMenuItem', 50],
           ],
        ];
    }

    /**
     * Add the show/create menu item.
     *
     * @param MenuCollectionEvent $event The menu collection event.
     * @return void
     */
    public function addProfileMenuItem(MenuCollectionEvent $event): void
    {
        $menu = $event->getMenu();
        $user = $this->token_storage->getToken()->getUser();

        // If this user has a profile link it, otherwise link to edit.
        if ($user->hasProfile()) {
            $directory = $this->factory->createItem('menu_item.profile_show', [
                'route' => 'bkstg_profile_read',
                'extras' => [
                    'icon' => 'user',
                    'translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN,
                ],
            ]);
        } else {
            $directory = $this->factory->createItem('menu_item.profile_create', [
                'route' => 'bkstg_profile_edit',
                'extras' => [
                    'icon' => 'user-plus',
                    'translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN,
                ],
            ]);
        }
        $menu->addChild($directory);
    }
}
