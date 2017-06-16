<?php

namespace AppBundle\Controller\Api\Admin\Venue;

use AppBundle\Entity\Venue\Venue;
use AppBundle\Util\DStore;
use AppBundle\Form\Admin\Venue\VenueType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class VenuesController extends FOSRestController
{

    /**
     * @View()
     */
    public function getVenuesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'name' );

        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $queryBuilder = $em->createQueryBuilder()->select( ['v'] )
                ->from( 'AppBundle\Entity\Venue\Venue', 'v' )
                ->orderBy( 'v.' . $dstore['sort-field'], $dstore['sort-direction'] );
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
        return $data;
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
                        ->getRepository( 'AppBundle\Entity\Venue\Venue' )->find( $id );
        if( $venue !== null )
        {
            $data = [
                'id' => $venue->getId(),
                'name' => $venue->getName(),
                'active' => $venue->isActive(),
                'address' => $venue->getAddress(),
                'directions' => $venue->getDirections(),
                'parking' => $venue->getParking(),
                'comment' => $venue->getComment(),
                'contacts' => $venue->getContacts( false )
            ];
            $formUtil = $this->get( 'app.util.form' );
            $formUtil->saveDataTimestamp( 'venue' . $venue->getId(), $venue->getUpdated() );
            $logUtil = $this->get( 'app.util.log' );
            $logUtil->getLog( 'AppBundle\Entity\Venue\VenueLog', $id );
            $data['history'] = $logUtil->translateIdsToText();
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
            $venue = $em->getRepository( 'AppBundle\Entity\Venue\Venue' )->find( $id );
            $formUtil = $this->get( 'app.util.form' );
            if( $formUtil->checkDataTimestamp( 'venue' . $venue->getId(), $venue->getUpdated() ) === false )
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
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $repository = $this->getDoctrine()
                ->getRepository( 'AppBundle\Entity\Venue\Venue' );
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
        $venue = $em->getRepository( 'AppBundle\Entity\Venue\Venue' )->find( $id );
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
