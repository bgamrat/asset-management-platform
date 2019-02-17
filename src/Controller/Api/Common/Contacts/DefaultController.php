<?php

Namespace App\Controller\Api\Common\Contacts;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
            $contactTypes = null;

            parse_str( $request->getQueryString(), $contactTypes );
            unset( $contactTypes['name'] );

            $em = $this->getDoctrine()->getManager();

            // Get existing people of the requested contact types
            $contacts = $em->getRepository( 'App\Entity\Common\Person' )->findByContactNameLike( $name, array_keys( $contactTypes ) );

            $people = $em->getRepository( 'App\Entity\Common\Person' )->findByEntityContactNameLike( $name, array_keys( $contactTypes ) );

            // Remove any people who are already contacts
            $peopleHashes = [];
            foreach( $people as $p )
            {
                $peopleHashes[] = $p->getHash();
            }
            foreach( $contacts as $i => $c )
            {
                if( in_array( $c->getHash(), $peopleHashes ) )
                {
                    unset( $contacts[$i] );
                }
            }

            return array_merge( $contacts, $people );
        }
    }

}
