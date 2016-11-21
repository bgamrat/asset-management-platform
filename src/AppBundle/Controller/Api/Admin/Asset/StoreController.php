<?php

namespace AppBundle\Controller\Api\Admin\Asset;

use AppBundle\Util\DStore;
use AppBundle\Entity\Asset\Model;
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
    public function getAddresstypesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder()->select( ['at.id', 'at.type'] )
                ->from( 'AppBundle\Entity\Common\AddressType', 'at' )
                ->orderBy( 'at.type' );
        $data = $queryBuilder->getQuery()->getResult();
        
        return $data;
    }

    
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
                    ->from( 'AppBundle\Entity\Asset\Manufacturer', 'm' )
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
    public function getCategoriesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $name = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['c.id', "c.name"] )
                    ->from( 'AppBundle\Entity\Asset\Category', 'c' )
                    ->where( "c.name LIKE :category_name" )
                    ->setParameter( 'category_name', $name );

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
    public function getEmailtypesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder()->select( ['e.id', 'e.type'] )
                ->from( 'AppBundle\Entity\Common\EmailType', 'e' )
                ->orderBy( 'e.type' );
        $data = $queryBuilder->getQuery()->getResult();
        
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
                    ->from( 'AppBundle\Entity\Asset\Manufacturer', 'm' )
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
                    ->from( 'AppBundle\Entity\Asset\Model', 'm' )
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
    public function getPersontypesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder()->select( ['pt.id', 'pt.type'] )
                ->from( 'AppBundle\Entity\Common\PersonType', 'pt' )
                ->orderBy( 'pt.type' );
        $data = $queryBuilder->getQuery()->getResult();
        
        return $data;
    }

    /**
     * @View()
     */
    public function getPhonetypesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder()->select( ['pt.id', 'pt.type'] )
                ->from( 'AppBundle\Entity\Common\PhoneNumberType', 'pt' )
                ->orderBy( 'pt.type' );
        $data = $queryBuilder->getQuery()->getResult();
        
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
                ->from( 'AppBundle\Entity\Asset\Model', 'm' )
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
                    ->from( 'AppBundle\Entity\Asset\Vendor', 'v' )
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
                    ->from( 'AppBundle\Entity\Asset\Trailer', 't' )
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
