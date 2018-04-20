<?php

Namespace App\Controller\Api\Common\People;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/people")
     */
    public function getPeopleAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        $last = $request->get( 'last' );
        if( !empty( $name ) )
        {
            $em = $this->getDoctrine()->getManager();
            $name = '%' . str_replace( '*', '%', $name );
            if( $last === '' )
            {
                $queryBuilder = $em->createQueryBuilder()->select( ['p.id',
                            'p.lastname AS name'] )
                        ->from( 'App\Entity\Common\Person', 'p' )
                        ->where( "LOWER(p.lastname) LIKE :name" )
                        ->orderBy( 'name' )
                        ->setParameter( 'name', strtolower( $name ) );
            }
            else
            {
                $queryBuilder = $em->createQueryBuilder()->select( ['p.id',
                            "CONCAT(p.firstname, ' ',COALESCE(CONCAT(p.middlename,' '),''), p.lastname) AS name"] )
                        ->from( 'App\Entity\Common\Person', 'p' )
                        ->where( "LOWER(CONCAT(p.firstname, ' ',COALESCE(CONCAT(p.middlename,' '),''), p.lastname)) LIKE :name" )
                        ->orderBy( 'name' )
                        ->setParameter( 'name', strtolower( $name ) );
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
