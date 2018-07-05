<?php

Namespace App\Controller\Api\Admin\Asset;

use App\Entity\Asset\Carrier;
use App\Util\DStore;
use App\Util\Form as FormUtil;
use App\Form\Admin\Asset\CarrierType;
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

class CarriersController extends FOSRestController
{

    private $dstore;
    private $formUtil;

    public function __construct( DStore $dstore, FormUtil $formUtil )
    {
        $this->dstore = $dstore;
        $this->formUtil = $formUtil;
    }

    /**
     * @View()
     */
    public function getCarriersAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->dstore->gridParams( $request, 'name' );

        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $queryBuilder = $em->createQueryBuilder()->select( ['c'] )
                ->from( 'App\Entity\Asset\Carrier', 'c' )
                ->leftJoin( 'c.contacts', 'cc' )
                ->orderBy( 'c.' . $dstore['sort-field'], $dstore['sort-direction'] );
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
                            $queryBuilder->expr()->like( 'LOWER(c.name)', '?1' )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( 'LOWER(c.name)', '?1' )
                    );
            }
            $queryBuilder->setParameter( 1, strtolower( $dstore['filter'][DStore::VALUE] ) );
        }
        $data = $queryBuilder->getQuery()->getResult();
        $count = $em->getRepository( 'App\Entity\Asset\Carrier' )->count([]);
        $view = FOSRestView::create();
        $view->setData( $data );
        $view->setHeader( 'Content-Range', 'items ' . $offset . '-' . ($offset + $limit) . '/' . $count );
        $handler = $this->get( 'fos_rest.view_handler' );
        return $handler->handle( $view );
    }

    /**
     * @View()
     */
    public function getCarrierAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $carrier = $this->getDoctrine()
                        ->getRepository( 'App\Entity\Asset\Carrier' )->find( $id );
        if( $carrier !== null )
        {

            $formUtil = $this->formUtil;
            $formUtil->saveDataTimestamp( 'carrier' . $carrier->getId(), $carrier->getUpdatedAt() );
            $form = $this->createForm( CarrierType::class, $carrier, ['allow_extra_fields' => true] );

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
    public function postCarrierAction( $id, Request $request )
    {
        return $this->putCarrierAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putCarrierAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $data = $request->request->all();
        if( $id === "null" )
        {
            $carrier = new Carrier();
        }
        else
        {
            $carrier = $em->getRepository( 'App\Entity\Asset\Carrier' )->find( $id );
            $formUtil = $this->formUtil;
            if( $formUtil->checkDataTimestamp( 'carrier' . $carrier->getId(), $carrier->getUpdatedAt() ) === false )
            {
                throw new Exception( "data.outdated", 400 );
            }
        }
        $form = $this->createForm( CarrierType::class, $carrier, ['allow_extra_fields' => true] );
        try
        {
            $form->submit( $data );
            if( $form->isValid() )
            {
                $carrier = $form->getData();
                $em->persist( $carrier );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_carrier_get_carrier', array('id' => $carrier->getId()), true // absolute
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
    public function patchCarrierAction( $id, Request $request )
    {
        $formProcessor = $this->formUtil;
        $data = $formProcessor->getJsonData( $request );
        $repository = $this->getDoctrine()
                ->getRepository( 'App\Entity\Asset\Carrier' );
        $carrier = $repository->find( $id );
        if( $carrier !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $carrier->setActive( $value );
                        break;
                }

                $em->persist( $carrier );
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteCarrierAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->enable( 'softdeleteable' );
        $carrier = $em->getRepository( 'App\Entity\Asset\Carrier' )->find( $id );
        if( $carrier !== null )
        {
            $em->remove( $carrier );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

}
