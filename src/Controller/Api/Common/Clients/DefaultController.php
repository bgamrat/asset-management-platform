<?php

Namespace App\Controller\Api\Common\Clients;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/clients")
     */
    public function getClientsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $name = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['c.id', "c.name"] )
                    ->from( 'App\Entity\Client\Client', 'c' )
                    ->where( "LOWER(c.name) LIKE :client_name" )
                    ->setParameter( 'client_name', strtolower( $name ) );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }
}
