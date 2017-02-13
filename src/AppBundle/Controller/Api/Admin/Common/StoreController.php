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

    /**
     * @View()
     */
    public function getCasesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $barcode_model = $request->get( 'name' );
        if( !empty( $barcode_model ) )
        {
            $barcode = '%' . str_replace( '*', '%', $barcode_model );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['a.id', "CONCAT(b.barcode,' ',m.name) AS name"] )
                    ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                    ->innerJoin( 'a.barcodes', 'b' )
                    ->innerJoin( 'a.model', 'm' );
            $queryBuilder
                    ->where( "LOWER(CONCAT(CONCAT(b.barcode, ' '), m.name)) LIKE :barcode_model" )
                    ->andWhere( 'm.container = true' )
                    ->setParameter( 'barcode_model', strtolower( $barcode_model ) );

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
                    ->where( "LOWER(c.name) LIKE :category_name" )
                    ->setParameter( 'category_name', strtolower( $name ) );

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
    public function getClientsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $name = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['c.id', "c.name"] )
                    ->from( 'AppBundle\Entity\Client\Client', 'c' )
                    ->where( "LOWER(c.name) LIKE :client_name" )
                    ->setParameter( 'client_name', strtolower( $name ) );

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
    public function getContractsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $clientContract = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['ct.id', "CONCAT(CONCAT(cl.name, ' '), ct.name) AS name"] )
                    ->from( 'AppBundle\Entity\Client\Contract', 'ct' )
                    ->innerJoin( 'ct.client', 'cl' )
                    ->where( "LOWER(CONCAT(CONCAT(cl.name, ' '), ct.name)) LIKE :client_contract" )
                    ->orderBy( 'name' )
                    ->setParameter( 'client_contract', strtolower( $clientContract ) );

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
    public function getContracttrailersAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        if( !empty( $id ) )
        {
            $contractRepository = $this->getDoctrine()
                    ->getRepository( 'AppBundle\Entity\Client\Contract' );

            $contract = $contractRepository->find( $id );
            $data = ['id' => $id,
                'required' => $contract->getRequiresTrailers( false ),
                'available' => $contract->getAvailableTrailers( false )];
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
                    ->where( "LOWER(CONCAT(CONCAT(b.name, ' '), m.name)) LIKE :brand_model" )
                    ->orderBy( 'name' )
                    ->setParameter( 'brand_model', strtolower( $brandModel ) );

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
    public function getTrailersAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $name = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['t.id', 't.name'] )
                    ->from( 'AppBundle\Entity\Asset\Trailer', 't' );
            $queryBuilder
                    ->where( $queryBuilder->expr()->like( 'LOWER(t.name)', ':name' ) )
                    ->setParameter( 'name', strtolower( $name ) );

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
    public function getTrailercontentsAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder()->select( 'a.id' )
                ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                ->innerJoin( 'a.model', 'm' )
                ->leftJoin( 'a.location', 'l' )
                ->leftJoin( 'l.type', 'lt' )
                ->where( "l.entity = ?1 AND lt.entity = 'trailer' AND m.container = true" );
        $queryBuilder->setParameter( 1, $id );
        $data = $queryBuilder->getQuery()->getResult();
        $containers = [];
        foreach( $data as $c )
        {
            $containers[] = $c['id'];
        }

        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'id' );
        switch( $dstore['sort-field'] )
        {
            case 'barcode':
                $sortField = 'bc.barcode';
                break;
            case 'model':
            case 'model_text':
                $sortField = 'm.name';
                break;
            case 'category':
                $sortField = 'c.name';
                break;
            default:
                $sortField = 'a.' . $dstore['sort-field'];
        }

        $columns = ['a.id', 'bc.barcode',
            "CONCAT(CONCAT(b.name,' '),m.name) AS model_text", 'm.id AS model', 'a.serial_number', 'a.location_text',
            's.name AS status_text', 'c.name AS category_text',
            'a.comment', 'a.active'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 'a.deletedAt AS deleted_at';
        }

        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                ->innerJoin( 'a.model', 'm' )
                ->innerJoin( 'm.category', 'c' )
                ->innerJoin( 'm.brand', 'b' )
                ->leftJoin( 'a.location', 'l' )
                ->leftJoin( 'l.type', 'lt' )
                ->leftJoin( 'a.barcodes', 'bc', 'WITH', 'bc.active = true' )
                ->leftJoin( 'a.status', 's' )
                ->where( "l.entity = ?1 AND lt.entity = 'trailer'" )
                ->orWhere( "l.entity IN (?2) AND lt.entity='asset'" )
                ->orderBy( $sortField, $dstore['sort-direction'] );
        $queryBuilder->setParameter( 1, $id );
        $queryBuilder->setParameter( 2, $containers );
        if( $dstore['limit'] !== null )
        {
            $queryBuilder->setMaxResults( $dstore['limit'] );
        }
        if( $dstore['offset'] !== null )
        {
            $queryBuilder->setFirstResult( $dstore['offset'] );
        }
        if( $dstore['filter'] !== null )
        {
            switch( $dstore['filter'][DStore::OP] )
            {
                case DStore::LIKE:
                    $queryBuilder->andWhere(
                            "LOWER(CONCAT(CONCAT(b.name,' '),m.name)) LIKE ?3 OR 
                            LOWER(c.name) LIKE ?3 OR LOWER(a.serial_number) LIKE ?3" );
                    break;
                case DStore::GT:
                    $queryBuilder->andWhere(
                            $queryBuilder->expr()->gt( "LOWER(CONCAT(CONCAT(b.name,' '),m.name))", '?3' )
                    );
            }
            $queryBuilder->setParameter( 3, strtolower( $dstore['filter'][DStore::VALUE] ) );
        }
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
                    ->where( "LOWER(v.name) LIKE :vendor_name" )
                    ->setParameter( 'vendor_name', strtolower( $name ) );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
