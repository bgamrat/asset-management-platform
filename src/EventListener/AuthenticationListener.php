<?php

namespace App\EventListener;

use FOS\UserBundle\EventListener\AuthenticationListener as FOSAuthenticationListener;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Security\LoginManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AuthenticationListener extends FOSAuthenticationListener {
    
    /**
     * @param FilterUserResponseEvent  $event
     * @param string                   $eventName
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function authenticate(FilterUserResponseEvent $event, $eventName, EventDispatcherInterface $eventDispatcher) {
        try {
            $this->loginManager->logInUser($this->firewallName, $event->getUser(), $event->getResponse());
            $eventDispatcher->dispatch(FOSUserEvents::SECURITY_IMPLICIT_LOGIN, new UserEvent($event->getUser(), $event->getRequest()));
        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
    }

}
