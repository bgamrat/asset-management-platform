<?php

namespace AppBundle\Controller\Api\Admin\Asset;

use AppBundle\Util\DStore;
use AppBundle\Entity\Asset\Trailer;
use AppBundle\Entity\Asset\Location;
use AppBundle\Form\Admin\Asset\TrailerType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class TrailersController extends FOSRestController
{

    /**
     * @View()
     */
    public function getTrailersAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'id' );
        switch( $dstore['sort-field'] )
        {
            case 'name':
                $sortField = 't.name';
                break;
            case 'location_text':
                $sortField = 't.location_text';
                break;
            case 'model':
            case 'model_text':
                $sortField = 'm.name';
                break;
            default:
                $sortField = 'a.' . $dstore['sort-field'];
        }
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $columns = ['t.id', 't.location_text', 't.name',
            "CONCAT(CONCAT(b.name,' '),m.name) AS model_text", 'm.id AS model', 't.serial_number',
            's.name AS status_text', 's.id AS status',
            't.comment', 't.active'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 't.deletedAt AS deleted_at';
        }
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle\Entity\Asset\Trailer', 't' )
                ->innerJoin( 't.model', 'm' )
                ->innerJoin( 'm.brand', 'b' )
                ->leftJoin( 't.status', 's' )
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
                            $queryBuilder->expr()->orX(
                                    $queryBuilder->expr()->orX(
                                            $queryBuilder->expr()->like( 'LOWER(t.name)', '?1' ), 
                                            $queryBuilder->expr()->like( 'LOWER(t.serial_number)', '?1' ) ), 
                                    $queryBuilder->expr()->like( 'LOWER(t.location_text)', '?1' )
                            )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( 'LOWER(m.name)', '?1' )
                    );
            }
            $queryBuilder->setParameter( 1, strtolower($dstore['filter'][DStore::VALUE]) );
        }
        $data = $queryBuilder->getQuery()->getResult();
        return array_values( $data );
    }

    /**
     * @View()
     */
    public function getTrailerAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $trailer = $this->getDoctrine()
                        ->getRepository( 'AppBundle\Entity\Asset\Trailer' )->find( $id );
        if( $trailer !== null )
        {
            $model = $trailer->getModel();
            $brand = $model->getBrand();
            $location = $trailer->getLocation();
            if( $location === null )
            {
                $location = new Location();
                $locationId = $locationType = null;
            }
            else
            {
                $locationId = $location->getId();
                $locationTypeId = $location->getType();
                $locationType = $this->getDoctrine()
                                ->getRepository( 'AppBundle\Entity\Asset\LocationType' )->find( $locationTypeId );
                ;
            }
            $relationships = [
                'extends' => $trailer->getExtends( false ),
                'requires' => $trailer->getRequires( false ),
                'extended_by' => $trailer->getExtendedBy( false ),
                'required_by' => $trailer->getRequiredBy( false )
            ];
            $status = $trailer->getStatus();
            $data = [
                'id' => $id,
                'model_text' => $brand->getName() . ' ' . $model->getName(),
                'model' => $model->getId(),
                'trailer_relationships' => $relationships,
                'serial_number' => $trailer->getSerialNumber(),
                'location_text' => $trailer->getLocationText(),
                'location' => [ 'id' => $locationId, 'entity' => $location->getEntity(), 'type' => $locationType],
                'status_text' => $status->getName(),
                'status' => $status->getId(),
                'name' => $trailer->getName(),
                'comment' => $trailer->getComment(),
                'purchased' => $trailer->getPurchased()->format('Y-m-d'),
                'cost' => $trailer->getCost(),
                'active' => $trailer->isActive()
            ];

            $logUtil = $this->get( 'app.util.log' );
            $logUtil->getLog( 'AppBundle\Entity\Asset\TrailerLog', $id );
            $data['history'] = $logUtil->translateIdsToText();
            $formUtil = $this->get( 'app.util.form' );
            $formUtil->saveDataTimestamp( 'trailer' . $trailer->getId(), $trailer->getUpdated() );
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
    public function postTrailerAction( $id, Request $request )
    {
        return $this->putTrailerAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putTrailerAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $data = $request->request->all();
        if( $id === "null" )
        {
            $trailer = new Trailer();
        }
        else
        {
            $trailer = $em->getRepository( 'AppBundle\Entity\Asset\Trailer' )->find( $id );
            $formUtil = $this->get( 'app.util.form' );
            if( $formUtil->checkDataTimestamp( 'trailer' . $trailer->getId(), $trailer->getUpdated() ) === false )
            {
                throw new Exception( "data.outdated", 400 );
            }
        }
        if( $trailer->getLocation() === null )
        {
            $trailer->setLocation( new Location() );
        }
        $form = $this->createForm( TrailerType::class, $trailer, ['allow_extra_fields' => true] );
        try
        {
            $form->submit( $data );
            if( $form->isValid() )
            {
                $trailer = $form->getData();
                $em->persist( $trailer );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_trailers_get_trailer', array('id' => $trailer->getId()), true // absolute
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
    public function patchTrailerAction( $id, Request $request )
    {
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'AppBundle\Entity\Asset\Trailer' );
        $trailer = $repository->find( $id );
        if( $trailer !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $trailer->setActive( $value );
                        break;
                }

                $em->persist( $trailer );
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteTrailerAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $trailer = $em->getRepository( 'AppBundle\Entity\Asset\Trailer' )->find( $id );
        if( $trailer !== null )
        {
            $em->getFilters()->enable( 'softdeleteable' );
            $em->remove( $trailer );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

}
