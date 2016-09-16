<?php

namespace AppBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DoctrineExtensionListener implements ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer( ContainerInterface $container = null )
    {
        $this->container = $container;
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
        $tokenStorage = $this->container->get( 'security.token_storage', ContainerInterface::NULL_ON_INVALID_REFERENCE );
        if( null !== $tokenStorage && null !== $tokenStorage->getToken() && null !== $tokenStorage->getToken()->getUser() )
        {           
            $loggable = $this->container->get( 'gedmo.listener.loggable' );
            $user = $tokenStorage->getToken()->getUser();
            $loggable->setUsername( $user->getUsername() );
        }
    }

}