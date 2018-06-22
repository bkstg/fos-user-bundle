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

    public function __construct(
        FactoryInterface $factory,
        TokenStorageInterface $token_storage
    ) {
        $this->factory = $factory;
        $this->token_storage = $token_storage;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
           UserMenuCollectionEvent::NAME => [
               ['addProfileMenuItem', 50],
           ],
        ];
    }

    public function addProfileMenuItem(MenuCollectionEvent $event)
    {
        $menu = $event->getMenu();
        $user = $this->token_storage->getToken()->getUser();

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
