<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgFOSUserBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\Search\EventSubscriber;

use Bkstg\SearchBundle\Event\FieldCollectionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FieldCollectionSubscriber implements EventSubscriberInterface
{
    /**
     * Return the subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FieldCollectionEvent::NAME => [
                ['addUserFields', 0],
            ],
        ];
    }

    /**
     * Add the users fields to the search.
     *
     * @param FieldCollectionEvent $event The collection event.
     *
     * @return void
     */
    public function addUserFields(FieldCollectionEvent $event): void
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
