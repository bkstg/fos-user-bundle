<?php

namespace Bkstg\FOSUserBundle\EventSubscriber;

use Bkstg\CoreBundle\Event\MenuCollectionEvent;
use Bkstg\CoreBundle\Event\ProductionMenuCollectionEvent;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProductionMenuSubscriber implements EventSubscriberInterface
{

    private $factory;
    private $url_generator;
    private $auth;

    public function __construct(
        FactoryInterface $factory,
        UrlGeneratorInterface $url_generator,
        AuthorizationCheckerInterface $auth
    ) {
        $this->factory = $factory;
        $this->url_generator = $url_generator;
        $this->auth = $auth;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
           ProductionMenuCollectionEvent::NAME => array(
               array('addMemberMenuItem', -50),
           )
        );
    }

    public function addMemberMenuItem(ProductionMenuCollectionEvent $event)
    {
        $menu = $event->getMenu();
        $group = $event->getGroup();

        if (!$this->auth->isGranted('GROUP_ROLE_ADMIN', $group)) {
            return;
        }

        // Create settings menu item.
        $settings = $menu->getChild('Settings');
        $members = $this->factory->createItem('Members', [
            'uri' => $this->url_generator->generate(
                'bkstg_membership_list',
                ['production_slug' => $group->getSlug()]
            ),
        ]);
        $settings->addChild($members);
    }
}
