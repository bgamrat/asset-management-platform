<?php

namespace AppBundle\Controller\Api\Common\Vendors;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/vendors")
     */
    public function getVendorsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_USER', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $em = $this->getDoctrine()->getManager();
            $vendors = $em->getRepository( 'AppBundle\Entity\Asset\Vendor' )->findByNameLike( $name );
            $data = [];
            foreach( $vendors as $v )
            {
                $contacts = $v->getContacts();
                if( !empty( $contacts ) )
                {
                    foreach( $contacts as $c )
                    {
                        $addresses = $c->getAddresses();
                        if( !empty( $addresses ) )
                        {
                            foreach( $addresses as $a )
                            {
                                $d = [];
                                $d['id'] = $v->getId();
                                $d['name'] = $v->getName();
                                // HTML label attributes for dijit.FilteringSelects MUST start with a tag
                                $d['label'] = '<div>'.$v->getName().'<br>'.$c->getFullName().'<br>'.nl2br( $a->getAddress() ).'</div>';
                                $data[] = $d;
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
