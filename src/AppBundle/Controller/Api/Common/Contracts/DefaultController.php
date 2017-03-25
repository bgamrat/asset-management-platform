<?php

namespace AppBundle\Controller\Api\Common\Contracts;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/contracts")
     */
    public function getContractsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $clientContract = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['ct.id', "CONCAT(CONCAT(cl.name, ' '), ct.name) AS name"] )
                    ->from( 'AppBundle\Entity\Client\Contract', 'ct' )
                    ->innerJoin( 'ct.client', 'cl' )
                    ->where( "LOWER(CONCAT(CONCAT(cl.name, ' '), ct.name)) LIKE :client_contract" )
                    ->orderBy( 'name' )
                    ->setParameter( 'client_contract', strtolower( $clientContract ) );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }
}
