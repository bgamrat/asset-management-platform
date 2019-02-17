<?php

Namespace App\Controller\Api\Common\ContactTypes;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/contacttypes")
     */
    public function getContacttypesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder()->select( ['c.id', 'c.entity'] )
                ->from( 'App\Entity\Common\ContactType', 'c' )
                ->orderBy( 'c.entity' );
        $data = $queryBuilder->getQuery()->getResult();

        return $data;
    }

}
