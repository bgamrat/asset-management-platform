<?php

Namespace App\Controller\Api\Common\CarrierServices;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/carrierservices")
     */
    public function getEventsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $serviceName = $request->get( 'name' );
        $carrierId = $request->get( 'carrier' );
        if( !empty( $serviceName ) )
        {
            $serviceName = '%' . str_replace( '*', '%', $serviceName );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['cs.id', "cs.name"] )
                    ->from( 'App\Entity\Asset\CarrierService', 'cs' )
                    ->innerJoin( 'cs.carrier', 'c' )
                    ->where( "LOWER(cs.name) LIKE :carrier_service_name AND c.id = :carrier_id" )
                    ->orderBy( 'cs.name' )
                    ->setParameter( 'carrier_service_name', strtolower( $serviceName ) )
                    ->setParameter( 'carrier_id', $carrierId );
            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }
}
