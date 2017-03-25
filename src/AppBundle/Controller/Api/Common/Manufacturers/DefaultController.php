<?php

namespace AppBundle\Controller\Api\Common\Manufacturers;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends FOSRestController
{

    /**
     * @Route("/api/store/manufacturers")
     */
    public function getManufacturersAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $name = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['m.id', "m.name"] )
                    ->from( 'AppBundle\Entity\Asset\Manufacturer', 'm' )
                    ->where( "LOWER(m.name) LIKE :manufacturer_name" )
                    ->setParameter( 'manufacturer_name', strtolower( $name ) );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
