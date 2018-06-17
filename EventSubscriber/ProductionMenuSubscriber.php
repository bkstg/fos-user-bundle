<?php

namespace Bkstg\FOSUserBundle\EventSubscriber;

use Bkstg\CoreBundle\Event\MenuCollectionEvent;
use Bkstg\CoreBundle\Event\ProductionMenuCollectionEvent;
use Bkstg\FOSUserBundle\BkstgFOSUserBundle;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProductionMenuSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $auth;

    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $auth
    ) {
        $this->factory = $factory;
        $this->auth = $auth;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
           ProductionMenuCollectionEvent::NAME => array(
               array('addProfileMenuItem', -10),
               array('addMemberMenuItem', -50),
           )
        );
    }

    public function addProfileMenuItem(ProductionMenuCollectionEvent $event)
    {
        $menu = $event->getMenu();
        $group = $event->getGroup();

        $directory = $this->factory->createItem('menu_item.directory', [
            'route' => 'bkstg_production_profile_list',
            'routeParameters' => ['production_slug' => $group->getSlug()],
            'extras' => [
                'icon' => 'users',
                'translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN,
            ],
        ]);
        $menu->addChild($directory);

    }

    public function addMemberMenuItem(ProductionMenuCollectionEvent $event)
    {
        $menu = $event->getMenu();
        $group = $event->getGroup();

        if ($this->auth->isGranted('GROUP_ROLE_ADMIN', $group)) {
            // Create settings menu item.
            $settings = $menu->getChild('menu_item.settings');
            $members = $this->factory->createItem('menu_item.members', [
                'route' => 'bkstg_membership_list',
                'routeParameters' => ['production_slug' => $group->getSlug()],
                'extras' => ['translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN],
            ]);
            $settings->addChild($members);
        }
    }
}
