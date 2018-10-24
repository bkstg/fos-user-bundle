<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgFOSUserBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\EventSubscriber;

use Bkstg\CoreBundle\Event\MainMenuCollectionEvent;
use Bkstg\CoreBundle\Event\MenuCollectionEvent;
use Bkstg\FOSUserBundle\BkstgFOSUserBundle;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MainMenuSubscriber implements EventSubscriberInterface
{
    private $factory;

    /**
     * Create a new main menu subscriber.
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
           MainMenuCollectionEvent::NAME => [
               ['addDirectoryMenuItem', 0],
           ],
        ];
    }

    /**
     * Create the main directory menu link.
     *
     * @param MenuCollectionEvent $event The menu collection event.
     *
     * @return void
     */
    public function addDirectoryMenuItem(MenuCollectionEvent $event): void
    {
        $menu = $event->getMenu();

        $directory = $this->factory->createItem('menu_item.directory', [
            'route' => 'bkstg_directory_index',
            'extras' => [
                'icon' => 'user',
                'translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN,
            ],
        ]);
        $menu->addChild($directory);
    }
}
