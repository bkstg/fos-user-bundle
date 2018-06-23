<?php

namespace Bkstg\FOSUserBundle\EventSubscriber;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResettingSuccessSubscriber implements EventSubscriberInterface
{
    private $url_generator;

    /**
     * Construct a resetting success subscriber.
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
    public static function getSubscribedEvents(): array
    {
        return [
            FOSUserEvents::RESETTING_RESET_SUCCESS => [
                ['generateResponse', 0],
            ]
        ];
    }

    /**
     * Generate a redirect response to the correct destination.
     *
     * @param  FormEvent $event The form event.
     * @return void
     */
    public function generateResponse(FormEvent $event): void
    {
        $event->setResponse(new RedirectResponse(
            $this->url_generator->generate('bkstg_profile_read')
        ));
    }
}
