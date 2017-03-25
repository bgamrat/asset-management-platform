<?php

namespace AppBundle\Controller\Api\Common\PhoneTypes;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/phonetypes")
     */
    public function getPhonetypesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder()->select( ['pt.id', 'pt.type'] )
                ->from( 'AppBundle\Entity\Common\PhoneNumberType', 'pt' )
                ->orderBy( 'pt.type' );
        $data = $queryBuilder->getQuery()->getResult();

        return $data;
    }

}
