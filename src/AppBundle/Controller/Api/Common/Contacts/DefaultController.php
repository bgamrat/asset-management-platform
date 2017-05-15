<?php

namespace AppBundle\Controller\Api\Common\Contacts;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;
use AppBundle\Controller\Api\Common\Common;

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
            $contacts = $em->getRepository( 'AppBundle\Entity\Common\Person' )->findByNameLike( $name );
            if( !empty( $contacts ) )
            {
                $common = new Common;
                $data = [];
                foreach( $contacts as $c )
                {
                    $data[$c->getId()] = $common->getContactDetails( $c );
                }
                $associations = [];

                $client = $request->get( 'client' );
                $venue = $request->get( 'venue' );
                if( $request->get( 'client' ) !== null )
                {
                    $associations = $em->getRepository( 'AppBundle\Entity\Client\Client' )->findByContacts( array_keys( $data ) );
                    foreach( $associations as $a )
                    {
                        $data[$a['contact_id']]['name'] = $a['name'] . '-' . $data[$a['contact_id']]['name'];
                    }
                }
                return array_values( $data );
            }
        }

        return null;
    }

}
