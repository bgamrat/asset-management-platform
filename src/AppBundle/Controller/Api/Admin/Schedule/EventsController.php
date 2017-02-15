<?php

namespace AppBundle\Controller\Api\Admin\Schedule;

use AppBundle\Form\Admin\Schedule\EventType;
use AppBundle\Entity\Schedule\Event;
use AppBundle\Util\DStore;
use FOS\RestBundle\Controller\FOSRestController;
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

    /**
     * @View()
     */
    public function getEventsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'name' );

        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $queryBuilder = $em->createQueryBuilder()->select( ['e'] )
                ->from( 'AppBundle\Entity\Schedule\Event', 'e' )
                ->orderBy( 'e.' . $dstore['sort-field'], $dstore['sort-direction'] );
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
                            $queryBuilder->expr()->like( 'LOWER(e.name)', '?1' )
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
            $cl = $e->getClient();
            $client_text = !empty( $cl ) ? $cl->getName() : null;
            $st = $e->getStart();
            $en = $e->getEnd();
            $item = [
                'id' => $e->getId(),
                'name' => $e->getName(),
                'client' => $client_text,
                'contacts' => $e->getContacts( false ),
                'tentative' => $e->isTentative(),
                'billable' => $e->isBillable(),
                'canceled' => $e->isCanceled(),
                'start' => !empty( $st ) ? $st->format( 'Y-m-d' ) : null,
                'end' => !empty( $en ) ? $en->format( 'Y-m-d' ) : null
            ];
            if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
            {
                $item['deleted_at'] = $e->getDeletedAt();
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
                        ->getRepository( 'AppBundle\Entity\Event\Event' )->find( $id );
        if( $event !== null )
        {
            $data = [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'active' => $event->isActive(),
                'comment' => $event->getComment(),
                'contacts' => $event->getContacts( false ),
                'contracts' => $event->getContracts( false ),
            ];
            $formUtil = $this->get( 'app.util.form' );
            $formUtil->saveDataTimestamp( 'event' . $event->getId(), $event->getUpdated() );
            return $data;
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
            $event = $em->getRepository( 'AppBundle\Entity\Event\Event' )->find( $id );
            $formUtil = $this->get( 'app.util.form' );
            if( $formUtil->checkDataTimestamp( 'event' . $event->getId(), $event->getUpdated() ) === false )
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
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $repository = $this->getDoctrine()
                ->getRepository( 'AppBundle\Entity\Event\Event' );
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
        $event = $em->getRepository( 'AppBundle\Entity\Event\Event' )->find( $id );
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
