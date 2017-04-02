<?php

namespace AppBundle\Controller\Api\Common\TimeSpanTypes;

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
    public function getPhonetypesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder()->select( ['ts.id', 'ts.name'] )
                ->from( 'AppBundle\Entity\Schedule\TimeSpanType', 'ts' )
                ->orderBy( 'ts.name' );
        $data = $queryBuilder->getQuery()->getResult();

        return $data;
    }

}
