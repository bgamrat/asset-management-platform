<?php

Namespace App\Controller\Api\Common\AddressTypes;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/addresstypes")
     */
    public function getAddresstypesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder()->select( ['at.id', 'at.type'] )
                ->from( 'App\Entity\Common\AddressType', 'at' )
                ->orderBy( 'at.type' );
        $data = $queryBuilder->getQuery()->getResult();

        return $data;
    }
}