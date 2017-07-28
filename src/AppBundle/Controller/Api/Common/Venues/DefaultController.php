<?php

namespace AppBundle\Controller\Api\Common\Venues;

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
                        // Get existing people of the requested contact types
            $contacts = $em->getRepository( 'AppBundle\Entity\Common\Person' )->findByContactNameLike( $name, [ 'venue' ] );

            $people = $em->getRepository( 'AppBundle\Entity\Common\Person' )->findByEntityContactNameLike( $name, [ 'venue' ]);

            $data = array_merge( $contacts, $people );
            }
        else
        {
            $data = null;
        }
        return $data;
    }

}
