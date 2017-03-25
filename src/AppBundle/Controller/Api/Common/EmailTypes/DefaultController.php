<?php

namespace AppBundle\Controller\Api\Common\EmailTypes;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/emailtypes")
     */
    public function getEmailtypesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder()->select( ['e.id', 'e.type'] )
                ->from( 'AppBundle\Entity\Common\EmailType', 'e' )
                ->orderBy( 'e.type' );
        $data = $queryBuilder->getQuery()->getResult();

        return $data;
    }

}
