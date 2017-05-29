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
            parse_str( $request->getQueryString(), $contactTypes );
            unset( $contactTypes['name'] );

            $em = $this->getDoctrine()->getManager();

            // Get existing contacts of the requested types
            $contacts = $em->getRepository( 'AppBundle\Entity\Common\Person' )->findByContactNameLike( $name, array_keys( $contactTypes ) );
            $data = [];
            if( !empty( $contacts ) )
            {
                foreach( $contacts as $c )
                {
                    $data = array_merge( $data, $c->getContactDetails() );
                }
            }

            $people = $em->getRepository( 'AppBundle\Entity\Common\Person' )->findByEntityContactNameLike( $name, array_keys( $contactTypes ) );

            if( !empty( $people ) )
            {
                foreach( $people as $p )
                {
                    $personContactDetails = $p->getContactDetails();
                    foreach( $personContactDetails as $pd )
                    {
                        if( !isset( $contacts[$pd['hash']] ) )
                        {
                            $data = array_merge( $data, $personContactDetails );
                        }
                    }
                }
                return array_values( $data );
            }
            return null;
        }
    }

}
