<?php

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
            ]
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
            ->addMust($qb->query()->term(['_type' => 'user']))
            ->addMust($qb->query()->term(['enabled' => 1]))
            ->addMust($qb->query()->term(['has_profile' => true]))
        ;
        $event->addFilter($query);
    }
}
