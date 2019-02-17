<?php

Namespace App\Controller\Api\Common\Manufacturers;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View;
use Controller\Api\Common\Person;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/manufacturers")
     */
    public function getManufacturersAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $em = $this->getDoctrine()->getManager();
            $manufacturers = $em->getRepository( 'App\Entity\Asset\Manufacturer' )->findByNameLike( $name );
            $common = new Common;
            $data = $common->getContacts($manufacturers);
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
