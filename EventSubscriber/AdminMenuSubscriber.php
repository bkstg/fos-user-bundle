<?php

namespace Bkstg\FOSUserBundle\EventSubscriber;

use Bkstg\CoreBundle\Event\AdminMenuCollectionEvent;
use Bkstg\CoreBundle\Event\MenuCollectionEvent;
use Bkstg\FOSUserBundle\BkstgFOSUserBundle;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminMenuSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FactoryInterface $factory) {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
           AdminMenuCollectionEvent::NAME => array(
               array('addUserMenuItem', -5),
           )
        );
    }

    public function addUserMenuItem(MenuCollectionEvent $event)
    {
        $menu = $event->getMenu();

        // Create users menu item.
        $users = $this->factory->createItem('menu_item.users', [
            'route' => 'bkstg_user_admin_index',
            'extras' => [
                'icon' => 'users',
                'translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN,
            ],
        ]);
        $menu->addChild($users);

        // Create users menu item.
        $users_list = $this->factory->createItem('menu_item.users', [
            'route' => 'bkstg_user_admin_index',
            'extras' => ['translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN],
        ]);
        $users->addChild($users_list);

        // Create users menu item.
        $users_archive = $this->factory->createItem('menu_item.archive', [
            'route' => 'bkstg_user_admin_archive',
            'extras' => ['translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN],
        ]);
        $users->addChild($users_archive);
    }
}
