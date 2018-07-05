<?php

Namespace App\Controller\Api\Admin\Asset;

use App\Entity\Asset\Vendor;
use App\Util\DStore;
use App\Util\Form as FormUtil;
use App\Util\Log;
use App\Form\Admin\Asset\VendorType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\View\View as FOSRestView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class VendorsController extends FOSRestController
{

    private $dstore;
    private $log;
    private $formUtil;

    public function __construct( DStore $dstore, Log $log, FormUtil $formUtil )
    {
        $this->dstore = $dstore;
        $this->log = $log;
        $this->formUtil = $formUtil;
    }

    /**
     * @View()
     */
    public function getVendorsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->dstore->gridParams( $request, 'name' );

        switch( $dstore['sort-field'] )
        {
            case 'brand_data':
                $sortField = 'b.name';
                break;
            default:
                $sortField = 'v.' . $dstore['sort-field'];
        }

        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $queryBuilder = $em->createQueryBuilder()->select( ['v'] )
                ->from( 'App\Entity\Asset\Vendor', 'v' )
                ->leftJoin( 'v.brands', 'b' )
                ->orderBy( $sortField, $dstore['sort-direction'] );
        $limit = 0;
        if( $dstore['limit'] !== null )
        {
            $limit = $dstore['limit'];
            $queryBuilder->setMaxResults( $limit );
        }
        $offset = 0;
        if( $dstore['offset'] !== null )
        {
            $offset = $dstore['offset'];
            $queryBuilder->setFirstResult( $offset );
        }
        if( $dstore['filter'] !== null )
        {
            switch( $dstore['filter'][DStore::OP] )
            {
                case DStore::LIKE:
                    $queryBuilder->where(
                            $queryBuilder->expr()->like( 'LOWER(v.name)', '?1' )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( 'LOWER(v.name)', '?1' )
                    );
            }
            $queryBuilder->setParameter( 1, strtolower( $dstore['filter'][DStore::VALUE] ) );
        }
        $query = $queryBuilder->getQuery();
        $vendorCollection = $query->getResult();
        $data = [];
        foreach( $vendorCollection as $v )
        {
            $item = [
                'id' => $v->getId(),
                'name' => $v->getName(),
                'brand_data' => $v->getBrands(),
                'active' => $v->isActive(),
            ];
            if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
            {
                $item['deleted_at'] = $v->getDeletedAt();
            }
            $data[] = $item;
        }
        $count = $em->getRepository( 'App\Entity\Asset\Vendor' )->count([]);
        $view = FOSRestView::create();
        $view->setData( $data );
        $view->setHeader( 'Content-Range', 'items ' . $offset . '-' . ($offset + $limit) . '/' . $count );
        $handler = $this->get( 'fos_rest.view_handler' );
        return $handler->handle( $view );
    }

    /**
     * @View()
     */
    public function getVendorAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $vendor = $this->getDoctrine()
                        ->getRepository( 'App\Entity\Asset\Vendor' )->find( $id );
        if( $vendor !== null )
        {
            $formUtil = $this->formUtil;
            $formUtil->saveDataTimestamp( 'vendor' . $vendor->getId(), $vendor->getUpdatedAt() );

            $form = $this->createForm( VendorType::class, $vendor, ['allow_extra_fields' => true] );

            return $form->getViewData();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @View()
     */
    public function postVendorAction( $id, Request $request )
    {
        return $this->putVendorAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putVendorAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $data = $request->request->all();
        if( $id === "null" )
        {
            $vendor = new Vendor();
        }
        else
        {
            $vendor = $em->getRepository( 'App\Entity\Asset\Vendor' )->find( $id );
            $formUtil = $this->formUtil;
            if( $formUtil->checkDataTimestamp( 'vendor' . $vendor->getId(), $vendor->getUpdatedAt() ) === false )
            {
                throw new Exception( "data.outdated", 400 );
            }
        }
        $form = $this->createForm( VendorType::class, $vendor, ['allow_extra_fields' => true] );
        try
        {
            $form->submit( $data );
            if( $form->isValid() )
            {
                $vendor = $form->getData();
                $em->persist( $vendor );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_vendor_get_vendor', array('id' => $vendor->getId()), true // absolute
                        )
                );
            }
            else
            {
                return $form;
            }
        }
        catch( Exception $e )
        {
            $response->setStatusCode( 400 );
            $response->setContent( json_encode(
                            ['message' => 'errors', 'errors' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTraceAsString()]
            ) );
        }
        return $response;
    }

    /**
     * @View(statusCode=204)
     */
    public function patchVendorAction( $id, Request $request )
    {
        $formProcessor = $this->formUtil;
        $data = $formProcessor->getJsonData( $request );
        $repository = $this->getDoctrine()
                ->getRepository( 'App\Entity\Asset\Vendor' );
        $vendor = $repository->find( $id );
        if( $vendor !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $vendor->setActive( $value );
                        break;
                }

                $em->persist( $vendor );
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteVendorAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->enable( 'softdeleteable' );
        $vendor = $em->getRepository( 'App\Entity\Asset\Vendor' )->find( $id );
        if( $vendor !== null )
        {
            $em->remove( $vendor );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

}
