<?php

namespace AppBundle\Controller\Api\Common\Vendors;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;
use AppBundle\Controller\Api\Common\Common;

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
            $common = new Common;
            $data = $common->getContacts($vendors);
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
