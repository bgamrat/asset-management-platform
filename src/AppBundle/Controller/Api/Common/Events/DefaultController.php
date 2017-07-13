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
        $venueId = $request->get( 'venue' );
        if( !empty( $eventName ) )
        {
            $eventName = '%' . str_replace( '*', '%', $eventName );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['e.id', "e.name"] )
                    ->from( 'AppBundle\Entity\Schedule\Event', 'e' )
                    ->leftJoin( 'e.client', 'cl' )
                    ->leftJoin( 'e.venue', 'v' )
                    ->where( "LOWER(e.name) LIKE :event_name" )
                    ->orderBy( 'e.name' )
                    ->setParameter( 'event_name', strtolower( $eventName ) );

            $andWhere = [];
            if( $clientId !== null )
            {
                $andWhere[] = 'cl.id = :client_id';
                $queryBuilder->setParameter( 'client_id', $clientId );
            }
            if( $venueId !== null )
            {
                $andWhere[] = 'v.id = :venue_id';
                $queryBuilder->setParameter( 'venue_id', $venueId );
            }
            if( count( $andWhere ) > 0 )
            {
                $queryBuilder->andWhere( '(' . implode( ' OR ', $andWhere ) . ')' );
            }
            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
