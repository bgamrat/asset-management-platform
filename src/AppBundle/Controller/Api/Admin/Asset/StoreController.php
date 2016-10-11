<?php

namespace AppBundle\Controller\Api\Admin\Asset;

use AppBundle\Util\DStore;
use AppBundle\Entity\Model;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class StoreController extends FOSRestController
{

    /**
     * @View()
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
                    ->from( 'AppBundle:Manufacturer', 'm' )
                    ->innerJoin( 'm.brands', 'b' )
                    ->where( "CONCAT(CONCAT(m.name, ' '), b.name) LIKE :manufacturer_brand" )
                    ->setParameter( 'manufacturer_brand', $manufacturerBrand );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

    /**
     * @View()
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
                    ->from( 'AppBundle:Manufacturer', 'm' )
                    ->where( "m.name LIKE :manufacturer_name" )
                    ->setParameter( 'manufacturer_name', $name );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

    /**
     * @View()
     */
    public function getModelsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $brandModel = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['m.id', "CONCAT(CONCAT(b.name, ' '), m.name) AS name"] )
                    ->from( 'AppBundle:Model', 'm' )
                    ->innerJoin( 'm.brand', 'b' )
                    ->where( "CONCAT(CONCAT(b.name, ' '), m.name) LIKE :brand_model" )
                    ->setParameter( 'brand_model', $brandModel );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

    /**
     * @View()
     */
    public function getModelAction( $brandModel )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder()->select( ['m.id', "CONCAT(CONCAT(b.name, ' '), m.name) AS name"] )
                ->from( 'AppBundle:Model', 'm' )
                ->innerJoin( 'm.brand', 'b' )
                ->where( "CONCAT(CONCAT(b.name, ' '), m.name) LIKE :brand_model" )
                ->setParameter( 'brand_model', $brandModel );

        $data = $queryBuilder->getQuery()->getResult();
        return $data;
    }

    /**
     * @View()
     */
    public function getVendorsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $name = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['v.id', "v.name"] )
                    ->from( 'AppBundle:Vendor', 'v' )
                    ->where( "v.name LIKE :vendor_name" )
                    ->setParameter( 'vendor_name', $name );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

    /**
     * @View()
     */
    public function getTrailersAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $name = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['t.id', "t.name"] )
                    ->from( 'AppBundle:Trailer', 't' )
                    ->where( "t.name LIKE :trailer_name" )
                    ->setParameter( 'trailer_name', $name );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
