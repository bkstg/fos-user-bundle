<?php

namespace Bkstg\FOSUserBundle\Search\EventSubscriber;

use Bkstg\SearchBundle\Event\FieldCollectionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FieldCollectionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FieldCollectionEvent::NAME => [
                ['addUserFields', 0],
            ]
        ];
    }

    public function addUserFields(FieldCollectionEvent $event)
    {
        $event->addFields([
            'username',
            'first_name',
            'last_name',
            'facebook',
            'twitter',
            'phone',
            'email',
        ]);
    }
}
