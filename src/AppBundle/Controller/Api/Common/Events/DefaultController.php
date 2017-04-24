<?php

namespace AppBundle\Controller\Api\Common\Events;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/events")
     */
    public function getEventsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $eventName = $request->get( 'name' );
        $clientId = $request->get( 'client' );
        if( !empty( $eventName ) )
        {
            $eventName = '%' . str_replace( '*', '%', $eventName );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['e.id', "e.name"] )
                    ->from( 'AppBundle\Entity\Schedule\Event', 'e' )
                    ->innerJoin( 'e.client', 'cl' )
                    ->where( "LOWER(e.name) LIKE :event_name AND cl.id = :client_id" )
                    ->orderBy( 'e.name' )
                    ->setParameter( 'event_name', strtolower( $eventName ) )
                    ->setParameter( 'client_id', $clientId );
            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }
}
