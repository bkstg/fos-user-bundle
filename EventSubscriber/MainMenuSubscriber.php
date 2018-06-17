<?php

namespace Bkstg\FOSUserBundle\EventSubscriber;

use Bkstg\CoreBundle\Event\MainMenuCollectionEvent;
use Bkstg\CoreBundle\Event\MenuCollectionEvent;
use Bkstg\FOSUserBundle\BkstgFOSUserBundle;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MainMenuSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FactoryInterface $factory) {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
           MainMenuCollectionEvent::NAME => [
               ['addDirectoryMenuItem', 0],
           ],
        ];
    }

    public function addDirectoryMenuItem(MenuCollectionEvent $event)
    {
        $menu = $event->getMenu();

        $directory = $this->factory->createItem('menu_item.directory', [
            'route' => 'bkstg_profile_list',
            'extras' => [
                'icon' => 'user',
                'translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN,
            ],
        ]);
        $menu->addChild($directory);
    }
}
