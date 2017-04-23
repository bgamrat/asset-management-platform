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

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $clientEvent = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['e.id', "e.name"] )
                    ->from( 'AppBundle\Entity\Schedule\Event', 'e' )
                    ->innerJoin( 'e.client', 'cl' )
                    ->where( "LOWER(CONCAT(CONCAT(cl.name, ' '), e.name)) LIKE :client_event" )
                    ->orderBy( 'e.name' )
                    ->setParameter( 'client_event', strtolower( $clientEvent ) );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }
}
