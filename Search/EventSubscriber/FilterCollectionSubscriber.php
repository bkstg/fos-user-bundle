<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgFOSUserBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\Search\EventSubscriber;

use Bkstg\SearchBundle\Event\FilterCollectionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FilterCollectionSubscriber implements EventSubscriberInterface
{
    /**
     * Return subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FilterCollectionEvent::NAME => [
                ['addUserFilter', 0],
            ],
        ];
    }

    /**
     * Create a query that filters to only active users with a profile.
     *
     * @param FilterCollectionEvent $event The filter collection event.
     */
    public function addUserFilter(FilterCollectionEvent $event): void
    {
        $qb = $event->getQueryBuilder();
        $query = $qb->query()->bool()
            ->addMust($qb->query()->term(['_index' => 'user']))
            ->addMust($qb->query()->term(['enabled' => true]))
            ->addMust($qb->query()->term(['has_profile' => true]))
        ;
        $event->addFilter($query);
    }
}
