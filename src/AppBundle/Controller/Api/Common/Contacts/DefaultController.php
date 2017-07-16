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

            // Get existing people of the requested contact types
            $contacts = $em->getRepository( 'AppBundle\Entity\Common\Person' )->findByContactNameLike( $name, array_keys( $contactTypes ) );

            $people = $em->getRepository( 'AppBundle\Entity\Common\Person' )->findByEntityContactNameLike( $name, array_keys( $contactTypes ) );

            return array_merge($contacts,$people);
        }
    }

}
