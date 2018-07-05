<?php

Namespace App\Controller\Api\Admin\Venue;

use App\Entity\Venue\Venue;
use App\Util\DStore;
use App\Util\Log;
use App\Util\Form as FormUtil;
use App\Form\Admin\Venue\VenueType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\View\View as FOSRestView;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class VenuesController extends FOSRestController
{

    private $dstore;
    private $log;
    private $formUtil;

    public function __construct( DStore $dstore, Log $log, FormUtil $formUtil ) {
        $this->dstore = $dstore;
        $this->log = $log;
        $this->formUtil = $formUtil;
    }
    /**
     * @View()
     */
    public function getVenuesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->dstore->gridParams( $request, 'name' );

        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $queryBuilder = $em->createQueryBuilder()->select( ['v'] )
                ->from( 'App\Entity\Venue\Venue', 'v' )
                ->orderBy( 'v.' . $dstore['sort-field'], $dstore['sort-direction'] );
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
        $venueCollection = $query->getResult();
        $data = [];
        foreach( $venueCollection as $v )
        {
            $item = [
                'id' => $v->getId(),
                'name' => $v->getName(),
                'address' => $v->getAddress(),
                'comment' => $v->getComment(),
                'active' => $v->isActive(),
            ];
            if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
            {
                $item['deleted_at'] = $v->getDeletedAt();
            }
            $data[] = $item;
        }
        $count = $em->getRepository( 'App\Entity\Venue\Venue' )->count([]);
        $view = FOSRestView::create();
        $view->setData( $data );
        $view->setHeader( 'Content-Range', 'items ' . $offset . '-' . ($offset + $limit) . '/' . $count );
        $handler = $this->get( 'fos_rest.view_handler' );
        return $handler->handle( $view );
    }

    /**
     * @View()
     */
    public function getVenueAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $venue = $this->getDoctrine()
                        ->getRepository( 'App\Entity\Venue\Venue' )->find( $id );
        if( $venue !== null )
        {
            $logUtil = $this->log;
            $logUtil->getLog( 'App\Entity\Venue\VenueLog', $id );
            $history = $logUtil->translateIdsToText();
            $formUtil = $this->formUtil;
            $formUtil->saveDataTimestamp( 'venue' . $venue->getId(), $venue->getUpdatedAt() );

            $form = $this->createForm( VenueType::class, $venue, ['allow_extra_fields' => true] );
            $venue->setHistory( $history );
            $form->add( 'history', TextareaType::class, ['data' => $history] );
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
    public function postVenueAction( $id, Request $request )
    {
        return $this->putVenueAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putVenueAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $data = $request->request->all();
        if( $id === "null" )
        {
            $venue = new Venue();
        }
        else
        {
            $venue = $em->getRepository( 'App\Entity\Venue\Venue' )->find( $id );
            $formUtil = $this->formUtil;
            if( $formUtil->checkDataTimestamp( 'venue' . $venue->getId(), $venue->getUpdatedAt() ) === false )
            {
                throw new Exception( "data.outdated", 400 );
            }
        }
        $form = $this->createForm( VenueType::class, $venue, ['allow_extra_fields' => true] );
        try
        {
            $form->submit( $data );
            if( $form->isValid() )
            {
                $venue = $form->getData();
                $em->persist( $venue );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_venue_get_venue', array('id' => $venue->getId()), true // absolute
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
    public function patchVenueAction( $id, Request $request )
    {
        $formProcessor = $this->formUtil;
        $data = $formProcessor->getJsonData( $request );
        $repository = $this->getDoctrine()
                ->getRepository( 'App\Entity\Venue\Venue' );
        $venue = $repository->find( $id );
        if( $venue !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $venue->setActive( $value );
                        break;
                }

                $em->persist( $venue );
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteVenueAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->enable( 'softdeleteable' );
        $venue = $em->getRepository( 'App\Entity\Venue\Venue' )->find( $id );
        if( $venue !== null )
        {
            $em->remove( $venue );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

}
