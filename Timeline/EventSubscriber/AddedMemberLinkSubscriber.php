<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgFOSUserBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\Timeline\EventSubscriber;

use Bkstg\TimelineBundle\Event\TimelineLinkEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AddedMemberLinkSubscriber implements EventSubscriberInterface
{
    private $url_generator;

    /**
     * Create a a new subscriber.
     *
     * @param UrlGeneratorInterface $url_generator The url generator service.
     */
    public function __construct(UrlGeneratorInterface $url_generator)
    {
        $this->url_generator = $url_generator;
    }

    /**
     * Return the subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            TimelineLinkEvent::NAME => [
                ['setAddedMemberLink', 0],
            ],
        ];
    }

    /**
     * Create the link for the added member timeline entry.
     *
     * @param TimelineLinkEvent $event The timeline entry event.
     *
     * @return void
     */
    public function setAddedMemberLink(TimelineLinkEvent $event): void
    {
        $action = $event->getAction();

        if ('added_member' != $action->getVerb()) {
            return;
        }

        $production = $action->getComponent('indirectComplement')->getData();
        $event->setLink($this->url_generator->generate('bkstg_production_read', [
            'production_slug' => $production->getSlug(),
        ]));
    }
}
