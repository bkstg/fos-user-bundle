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
     * @param  MenuCollectionEvent $event The menu collection event.
     * @return void
     */
    public function addDirectoryMenuItem(MenuCollectionEvent $event)
    {
        $menu = $event->getMenu();

        $directory = $this->factory->createItem('menu_item.directory', [
            'route' => 'bkstg_directory_list',
            'extras' => [
                'icon' => 'user',
                'translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN,
            ],
        ]);
        $menu->addChild($directory);
    }
}
