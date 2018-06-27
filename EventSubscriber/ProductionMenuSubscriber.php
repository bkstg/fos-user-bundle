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

    /**
     * Create a new production menu subscriber.
     *
     * @param FactoryInterface              $factory The menu factory service.
     * @param AuthorizationCheckerInterface $auth    The authorization checker service.
     */
    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $auth
    ) {
        $this->factory = $factory;
        $this->auth = $auth;
    }

    /**
     * Return the subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
           ProductionMenuCollectionEvent::NAME => [
               ['addDirectoryMenuItem', -10],
               ['addMemberMenuItem', -50],
           ]
        ];
    }

    /**
     * Add the directory menu item.
     *
     * @param ProductionMenuCollectionEvent $event The menu collection event.
     * @return void
     */
    public function addDirectoryMenuItem(ProductionMenuCollectionEvent $event): void
    {
        $menu = $event->getMenu();
        $group = $event->getGroup();

        $directory = $this->factory->createItem('menu_item.directory', [
            'route' => 'bkstg_production_directory_index',
            'routeParameters' => ['production_slug' => $group->getSlug()],
            'extras' => [
                'icon' => 'users',
                'translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN,
            ],
        ]);
        $menu->addChild($directory);
    }

    /**
     * Add the members menu item under settings for admins.
     *
     * @param ProductionMenuCollectionEvent $event The menu collection event.
     * @return void
     */
    public function addMemberMenuItem(ProductionMenuCollectionEvent $event): void
    {
        $menu = $event->getMenu();
        $group = $event->getGroup();

        if ($this->auth->isGranted('GROUP_ROLE_ADMIN', $group)) {
            // Create settings menu item.
            $settings = $menu->getChild('menu_item.settings');
            $members = $this->factory->createItem('menu_item.members', [
                'route' => 'bkstg_production_membership_index',
                'routeParameters' => ['production_slug' => $group->getSlug()],
                'extras' => ['translation_domain' => BkstgFOSUserBundle::TRANSLATION_DOMAIN],
            ]);
            $settings->addChild($members);
        }
    }
}
