<?php

Namespace App\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Gedmo\Loggable\LoggableListener;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DoctrineExtensionListener
{

    private $loggableListener;
    private $tokenStorage;

    public function __construct( LoggableListener $loggableListener, TokenStorageInterface $tokenStorage )
    {
        $this->loggableListener = $loggableListener;
        $this->tokenStorage = $tokenStorage;
    }

    public function onLateKernelRequest( GetResponseEvent $event )
    {
        //$translatable = $this->container->get('gedmo.listener.translatable');
        //$translatable->setTranslatableLocale($event->getRequest()->getLocale());
    }

    public function onConsoleCommand()
    {
        //$this->container->get('gedmo.listener.translatable')
        //  ->setTranslatableLocale($this->container->get('translator')->getLocale());
    }

    public function onKernelRequest( GetResponseEvent $event )
    {
        if( null !== $this->tokenStorage && null !== $this->tokenStorage->getToken() && !($this->tokenStorage->getToken() instanceof AnonymousToken ) )
        {
            $loggable = $this->loggableListener;
            $user = $this->tokenStorage->getToken()->getUser();
            $loggable->setUsername( $user->getUsername() );
        }
    }

}
