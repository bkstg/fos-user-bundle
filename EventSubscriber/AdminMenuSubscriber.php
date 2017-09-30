<?php

namespace Bkstg\FOSUserBundle\EventSubscriber;

use Bkstg\CoreBundle\Event\AdminMenuCollectionEvent;
use Bkstg\CoreBundle\Event\MenuCollectionEvent;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminMenuSubscriber implements EventSubscriberInterface
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
        $users = $this->factory->createItem('Users', [
            'uri' => $this->url_generator->generate('bkstg_user_admin_list'),
            'extras' => ['icon' => 'users'],
        ]);
        $menu->addChild($users);
    }
}
