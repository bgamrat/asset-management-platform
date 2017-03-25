<?php

namespace AppBundle\Controller\Api\Common\Brands;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends FOSRestController
{

    /**
     * @Route("/api/store/brands")
     */
    public function getBrandsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $manufacturerBrand = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['b.id', "CONCAT(CONCAT(m.name, ' '), b.name) AS name"] )
                    ->from( 'AppBundle\Entity\Asset\Manufacturer', 'm' )
                    ->innerJoin( 'm.brands', 'b' )
                    ->where( "LOWER(CONCAT(CONCAT(m.name, ' '), b.name)) LIKE :manufacturer_brand" )
                    ->setParameter( 'manufacturer_brand', strtolower( $manufacturerBrand ) );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
