<?php

Namespace App\Controller\Api\Common\Venues;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/venues")
     */
    public function getVenuesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_USER', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $em = $this->getDoctrine()->getManager();
            $data = $em->getRepository( 'Entity\Venue\Venue' )->findByNameLike( $name );
            }
        else
        {
            $data = null;
        }
        return $data;
    }

}
