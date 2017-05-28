<?php

namespace AppBundle\Controller\Api\Common\Contacts;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/contacts")
     */
    public function getContactsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $em = $this->getDoctrine()->getManager();

            $contacts = $em->getRepository( 'AppBundle\Entity\Common\Person' )->findByContactNameLike( $name );

            $client = $request->get( 'client' );
            $venue = $request->get( 'venue' );
            if( $request->get( 'client' ) !== null )
            {
                $contacts += $em->getRepository( 'AppBundle\Entity\Common\Person' )->findByClientContactNameLike( $name );
            }
            if( $request->get( 'manufacturer' ) !== null )
            {
                $contacts += $em->getRepository( 'AppBundle\Entity\Common\Person' )->findByManufacturerContactNameLike( $name );
            }
            if( !empty( $contacts ) )
            {
                $data = [];
                foreach( $contacts as $c )
                {
                    $data = array_merge( $data, $c->getContactDetails( $c ) );
                }
                return array_values( $data );
            }
        }
        return null;
    }

}
