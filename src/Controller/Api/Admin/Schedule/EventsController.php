<?php

Namespace App\Controller\Api\Admin\Schedule;

use App\Form\Admin\Schedule\EventType;
use App\Entity\Schedule\Event;
use App\Util\DStore;
use App\Util\Log;
use App\Util\Form as FormUtil;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class EventsController extends FOSRestController
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
    public function getEventsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $dstore = $this->dstore->gridParams( $request, 'id' );
        switch( $dstore['sort-field'] )
        {
            case 'client_text':
                $sortField = 'c.name';
                break;
            case 'venue_text':
                $sortField = 'v.name';
                break;
            case 'brand':
                $sortField = 'b.name';
                break;
            case 'trailer_text':
                $sortField = 't.name';
                break;
            case 'dates':
                $sortField = 'e.start';
                break;
            default:
                $sortField = 'e.' . $dstore['sort-field'];
        }
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $queryBuilder = $em->createQueryBuilder()->select( ['e.id', 'e.name',
                    'e.tentative', 'e.billable', 'e.canceled', 'e.start', 'e.end', 'e.deletedAt',
                    'c.name AS client_name', 'v.name AS venue_name'] )
                ->from( 'App\Entity\Schedule\Event', 'e' )
                ->leftJoin( 'e.client', 'c' )
                ->leftJoin( 'e.venue', 'v' )
                ->leftJoin( 'e.trailers', 't' )
                ->orderBy( $sortField, $dstore['sort-direction'] );
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
                    $queryBuilder->where(
                            $queryBuilder->expr()->orX( $queryBuilder->expr()->like( 'LOWER(e.name)', '?1' ), $queryBuilder->expr()->like( 'LOWER(t.name)', '?1' ) )
                            //$queryBuilder->expr()->like( 'LOWER(v.name)', '?1' )
                            //$queryBuilder->expr()->like( 'LOWER(e.name)', '?1' )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( 'LOWER(e.name)', '?1' )
                    );
            }
            $queryBuilder->setParameter( 1, strtolower( $dstore['filter'][DStore::VALUE] ) );
        }
        $query = $queryBuilder->getQuery();
        $eventCollection = $query->getResult();
        $data = [];
        foreach( $eventCollection as $e )
        {
            $event = $em->getRepository( 'App\Entity\Schedule\Event' )->find( $e['id'] );
            $trailers = $event->getTrailers();
            $trailerList = array_column( $trailers, 'name' );
            $contracts = $event->getContracts();
            foreach( $contracts as $c )
            {
                foreach( $c->getTrailers( 'requiresTrailers', false ) as $t )
                {
                    $trailerList[] = $t['name'];
                }
            }
            $contractList = array_column( $contracts, 'id' );
            $contractTrailers = $em->getRepository( 'App\Entity\Client\Contract' )
                    ->findBy( ['id' => $contractList] );
            $client_text = $e['client_name'];
            $venue_text = $e['venue_name'];
            $st = $e['start'];
            $en = $e['end'];
            $item = [
                'id' => $e['id'],
                'name' => $e['name'],
                'client_text' => $client_text,
                'venue_text' => $venue_text,
                'trailer_text' => implode( ',', $trailerList ),
                'tentative' => $e['tentative'],
                'billable' => $e['billable'],
                'canceled' => $e['canceled'],
                'start' => !empty( $st ) ? $st->format( 'Y-m-d' ) : null,
                'end' => !empty( $en ) ? $en->format( 'Y-m-d' ) : null
            ];
            if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
            {
                $item['deleted_at'] = $e['deletedAt'];
            }
            $data[] = $item;
        }
        return $data;
    }

    /**
     * @View()
     */
    public function getEventAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $event = $this->getDoctrine()
                        ->getRepository( 'App\Entity\Schedule\Event' )->find( $id );
        if( $event !== null )
        {
            $logUtil = $this->log;
            $logUtil->getLog( 'App\Entity\Schedule\EventLog', $id );
            $history = $logUtil->translateIdsToText();
            $formUtil = $this->formUtil;
            $formUtil->saveDataTimestamp( 'event' . $event->getId(), $event->getUpdatedAt() );

            $form = $this->createForm( EventType::class, $event, ['allow_extra_fields' => true] );
            $event->setHistory( $history );
            $form->add( 'history', TextareaType::class, ['data' => $history] );
            return $form->getViewData();

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
    public function postEventAction( $id, Request $request )
    {
        return $this->putEventAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putEventAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $data = $request->request->all();
        if( $id === "null" )
        {
            $event = new Event();
        }
        else
        {
            $event = $em->getRepository( 'App\Entity\Schedule\Event' )->find( $id );
            $formUtil = $this->formUtil;
            if( $formUtil->checkDataTimestamp( 'event' . $event->getId(), $event->getUpdatedAt() ) === false )
            {
                throw new Exception( "data.outdated", 400 );
            }
        }
        $form = $this->createForm( EventType::class, $event, ['allow_extra_fields' => true] );
        try
        {
            $form->submit( $data );
            if( $form->isValid() )
            {
                $event = $form->getData();
                $em->persist( $event );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_schedule_get_event', array('id' => $event->getId()), true // absolute
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
    public function patchEventAction( $id, Request $request )
    {
        $formProcessor = $this->formUtil;
        $data = $formProcessor->getJsonData( $request );
        $repository = $this->getDoctrine()
                ->getRepository( 'App\Entity\Event\Event' );
        $event = $repository->find( $id );
        if( $event !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $event->setActive( $value );
                        break;
                }

                $em->persist( $event );
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteEventAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->enable( 'softdeleteable' );
        $event = $em->getRepository( 'App\Entity\Event\Event' )->find( $id );
        if( $event !== null )
        {
            $em->remove( $event );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

}
