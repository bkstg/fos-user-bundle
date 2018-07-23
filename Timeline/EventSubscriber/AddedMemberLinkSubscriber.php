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

    public function __construct(UrlGeneratorInterface $url_generator)
    {
        $this->url_generator = $url_generator;
    }

    public static function getSubscribedEvents()
    {
        return [
            TimelineLinkEvent::NAME => [
                ['setAddedMemberLink', 0],
            ],
        ];
    }

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
