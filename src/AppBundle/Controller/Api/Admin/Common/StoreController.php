<?php

namespace AppBundle\Controller\Api\Admin\Common;

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
use FOS\RestBundle\Controller\Annotations\Get;

class StoreController extends FOSRestController
{

    private $entityMenus;

    public function __construct( Array $entityMenus )
    {
        $this->entityMenus = $entityMenus;
    }

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
     * @Get("/adminmenus", name="app_admin_api_store_get_adminmenu")
     * @Get("/adminmenus/", name="app_admin_api_store_get_adminmenu_alt")
     * @Get("/adminmenus/?parent={parent}", name="app_admin_api_store_get_adminmenu_parent", defaults={"parent" = "admin"})
     * @Get("/adminmenus/{id}", name="app_admin_api_store_get_adminmenu_id", defaults={"id" = "admin"})
     *
     */
    public function getAdminmenuAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $adminMenu = $this->get( 'app.menu_builder' )->createAdminMenu( [] );
        $renderer = $this->get( 'app.menu_renderer' );
        $menu = [];
        $id = $request->get( 'id' );
        if( $id !== null )
        {
            if( !in_array( $id, $this->entityMenus ) && preg_match( '/\-\d+$/', $id ) === 0 )
            {
                $menu = $renderer->render( $adminMenu, ['depth' => 1] );
                $menu = $menu[$id];
            }
            else
            {
                $dynamicId = trim( ucfirst( preg_replace( '/^([a-z]+).*$/', '$1', $id ) ), 's' );
                $menuMethod = 'get' . $dynamicId . 'Menu';
                if( method_exists( $this, $menuMethod ) )
                {
                    $menu = $this->{$menuMethod}( $adminMenu, $renderer, $id );
                }
            }
        }
        $parent = $request->get( 'parent' );
        if( $parent !== null )
        {
            foreach( $adminMenu as $name => $children )
            {
                if( $name === $parent )
                {
                    $menu['id'] = $parent;
                    $menu['name'] = $name;
                    $menu['children'] = $renderer->render( $children, ['depth' => 1], 'json' );

                    break;
                }
            }
        }
        if( isset( $menu['children'] ) )
        {
            foreach( $menu['children'] as $c => $child )
            {
                if( in_array( $child['id'], $this->entityMenus ) )
                {
                    $menu['children'][$c]['has_children'] = true;
                }
            }
        }
        return $menu;
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
    public function getCasesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $barcode = $request->get( 'name' );
        if( !empty( $barcode ) )
        {
            $barcode = '%' . str_replace( '*', '%', $barcode );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['a.id', "CONCAT(b.barcode,' ',m.name) AS name"] )
                    ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                    ->innerJoin( 'a.barcodes', 'b' )
                    ->innerJoin( 'a.model', 'm' );
            $queryBuilder
                    ->where( $queryBuilder->expr()->like( 'b.barcode', ':barcode' ) )
                    ->andWhere( 'm.container = true' )
                    ->setParameter( 'barcode', $barcode );

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

            $queryBuilder = $em->createQueryBuilder()->select( ['c.id', "c.fullName AS name"] )
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

    function getLocationMenu( $adminMenu, $renderer, $id )
    {
        $limit = 2500; // TODO: Change to deliver first letters if there are too many manfacturers
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
                ->select( ["CONCAT('manufacturer-',m.id) AS id", 'm.name', "'manufacturer' AS parent", 'COUNT(b.id) AS has_children'] )
                ->from( 'AppBundle\Entity\Asset\Manufacturer', 'm' )
                ->leftJoin( 'm.brands', 'b' )
                ->orderBy( 'm.name' )
                ->groupBy( 'm.id' )
                ->setFirstResult( 0 )
                ->setMaxResults( $limit );
        $menu = $renderer->render( $adminMenu['admin']['admin-assets']['manufacturers'] );
        $children = $queryBuilder->getQuery()->getResult();
        $l = count( $children );
        if( $l < $limit )
        {
            for( $i = 0; $i < $l; $i++ )
            {
                $children[$i]['uri'] = $this->generateUrl(
                        'app_admin_asset_manufacturer_index', ['name' => $children[$i]['name']], true ); // absolute
            }
        }
        $menu['children'] = $children;
        return $menu;
    }

    function getLocationTree( $adminMenu, $renderer, $id )
    {
        $manufacturer = $em->getRepository( 'AppBundle\Entity\Asset\Manufacturer' )->find( $base[1] );
        $brands = $manufacturer->getBrands();
        $children = [];
        foreach( $brands as $b )
        {
            $children[] = [
                'id' => $b->getId(),
                'name' => $b->getName(),
                'parent' => $id,
                'uri' => $this->generateUrl(
                        'app_admin_asset_manufacturer_getmanufacturerbrand', ['mname' => $manufacturer->getName(), 'bname' => $b->getName()], true ), // absolute
                'has_children' => false,
                'children' => null];
        }
        $menu['children'] = $children;

        return $menu;
    }
    
    function getManufacturerMenu( $adminMenu, $renderer, $id )
    {
        $limit = 2500; // TODO: Change to deliver first letters if there are too many manfacturers
        $em = $this->getDoctrine()->getManager();
        $base = explode( '-', $id );
        switch( $base[0] )
        {
            case 'manufacturers':
                $queryBuilder = $em->createQueryBuilder();
                $queryBuilder
                        ->select( ["CONCAT('manufacturer-',m.id) AS id", 'm.name', "'manufacturer' AS parent", 'COUNT(b.id) AS has_children'] )
                        ->from( 'AppBundle\Entity\Asset\Manufacturer', 'm' )
                        ->leftJoin( 'm.brands', 'b' )
                        ->orderBy( 'm.name' )
                        ->groupBy( 'm.id' )
                        ->setFirstResult( 0 )
                        ->setMaxResults( $limit );
                $menu = $renderer->render( $adminMenu['admin']['admin-assets']['manufacturers'] );
                $children = $queryBuilder->getQuery()->getResult();
                $l = count( $children );
                if( $l < $limit )
                {
                    for( $i = 0; $i < $l; $i++ )
                    {
                        $children[$i]['uri'] = $this->generateUrl(
                                'app_admin_asset_manufacturer_index', ['name' => $children[$i]['name']], true ); // absolute
                    }
                }
                $menu['children'] = $children;
                break;
            case 'manufacturer':
                $manufacturer = $em->getRepository( 'AppBundle\Entity\Asset\Manufacturer' )->find( $base[1] );
                $brands = $manufacturer->getBrands();
                $children = [];
                foreach( $brands as $b )
                {
                    $children[] = [
                        'id' => $b->getId(),
                        'name' => $b->getName(),
                        'parent' => $id,
                        'uri' => $this->generateUrl(
                                'app_admin_asset_manufacturer_getmanufacturerbrand', ['mname' => $manufacturer->getName(), 'bname' => $b->getName()], true ), // absolute
                        'has_children' => false,
                        'children' => null];
                }
                $menu['children'] = $children;
                break;
        }
        return $menu;
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
    public function getTrailerAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $name = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['a.id', "b.barcode AS name"] )
                    ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                    ->innerJoin( 'a.barcodes', 'b' )
                    ->innerJoin( 'a.model', 'm' );
            $queryBuilder
                    ->where( $queryBuilder->expr()->like( 'b.barcode', ':name' ) )
                    ->andWhere( 'm.container = true' )
                    ->setParameter( 'name', $name );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}