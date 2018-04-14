<?php

Namespace App\Controller\Api\Common\TimeSpanTypes;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/timespantypes")
     */
    public function getTimeSpanTypesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $name = str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['ts.id', "ts.name"] )
                    ->from( 'Entity\Schedule\TimeSpanType', 'ts' )
                    ->where( 'LOWER(ts.name) LIKE :time_span_name' )
                    ->orderBy( 'ts.name')
                    ->setParameter( 'time_span_name', strtolower( $name ) );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
