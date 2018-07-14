<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgFOSUserBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\EventSubscriber;

use Bkstg\CoreBundle\Event\AdminMenuCollectionEvent;
use Bkstg\CoreBundle\Event\MenuCollectionEvent;
use Bkstg\FOSUserBundle\BkstgFOSUserBundle;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminMenuSubscriber implements EventSubscriberInterface
{
    private $factory;

    /**
     * Create a new admin menu subscriber.
     *
     * @param FactoryInterface $factory The menu factory service.
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Return the subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
           AdminMenuCollectionEvent::NAME => [
               ['addUserMenuItem', -5],
           ],
        ];
    }

    /**
     * Add user admin menu items.
     *
     * @param MenuCollectionEvent $event The admin menu collection event.
     */
    public function addUserMenuItem(MenuCollectionEvent $event): void
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

        // Create index menu item.
        $users_list = $this->factory->createItem('menu_item.users', [
            'route' => 'bkstg_user_admin_index',
            'extras' => ['translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN],
        ]);
        $users->addChild($users_list);

        // Create archive menu item.
        $users_archive = $this->factory->createItem('menu_item.archive', [
            'route' => 'bkstg_user_admin_archive',
            'extras' => ['translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN],
        ]);
        $users->addChild($users_archive);
    }
}
