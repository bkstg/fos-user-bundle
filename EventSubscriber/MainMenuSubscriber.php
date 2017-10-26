<?php

namespace Bkstg\FOSUserBundle\EventSubscriber;

use Bkstg\CoreBundle\Event\MainMenuCollectionEvent;
use Bkstg\CoreBundle\Event\MenuCollectionEvent;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MainMenuSubscriber implements EventSubscriberInterface
{

    private $factory;
    private $url_generator;

    public function __construct(
        FactoryInterface $factory,
        UrlGeneratorInterface $url_generator
    ) {
        $this->factory = $factory;
        $this->url_generator = $url_generator;
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

        $directory = $this->factory->createItem('Directory', [
            'uri' => $this->url_generator->generate('bkstg_profile_list'),
            'extras' => ['icon' => 'user'],
        ]);
        $menu->addChild($directory);
    }
}
