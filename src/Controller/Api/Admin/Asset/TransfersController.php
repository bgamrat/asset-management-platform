<?php

Namespace App\Controller\Api\Admin\Asset;

use Util\DStore;
use Entity\Asset\Transfer;
use Entity\Asset\Trailer;
use Entity\Common\Person;
use Entity\Asset\Location;
use Form\Admin\Asset\TransferType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class TransfersController extends FOSRestController
{

    /**
     * @View()
     */
    public function getTransfersAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'id' );
        switch( $dstore['sort-field'] )
        {
            case 'barcode':
                $sortField = 'bc.barcode';
                break;
            case 'status_text':
                $sortField = 's.name';
                break;
            case 'carrier_text':
                $sortField = 'c.name';
                break;
            default:
                $sortField = 't.' . $dstore['sort-field'];
        }
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $columns = ['i'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 't.deletedAt AS deleted_at';
        }
        $transferIds = [];
        if( !empty( $dstore['filter'][DStore::VALUE] ) )
        {
            $assetData = $em->getRepository( 'Entity\Asset\Asset' )->findByBarcode( $dstore['filter'][DStore::VALUE] );
            if( !empty( $assetData ) )
            {
                $assetIds = [];
                foreach( $assetData as $a )
                {
                    $assetIds[] = $a->getId();
                }
                $queryBuilder = $em->createQueryBuilder()->select( 't.id' )
                        ->from( 'Entity\Asset\Transfer', 't' )
                        ->join( 't.items', 'ti' )
                        ->join( 'ti.asset', 'a' );
                $queryBuilder->where( 'a.id IN (:asset_ids)' );
                $queryBuilder->setParameter( 'asset_ids', $assetIds );
                $transferData = $queryBuilder->getQuery()->getResult();
                $transferIds = [];
                foreach( $transferData as $i )
                {
                    $transferIds[] = $i['id'];
                }
            }
        }

        $columns = ['t.id', 't.instructions', 's.name AS status_text', 't.source_location_text', 't.destination_location_text',
           'c.name AS carrier_text', 't.tracking_number', 'c.tracking_url'];
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'Entity\Asset\Transfer', 't' )
                ->join( 't.status', 's' )
                ->leftJoin( 't.carrier', 'c' )
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
                                            $queryBuilder->expr()->like( 'LOWER(t.instructions)', ':filter' ), $queryBuilder->expr()->like( 'LOWER(fm.lastname)', ':filter' ) ), $queryBuilder->expr()->like( 'LOWER(to.lastname)', ':filter' )
                            )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( 'LOWER(t.instructions)', ':filter' )
                    );
            }
            $queryBuilder->setParameter( 'filter', strtolower( $dstore['filter'][DStore::VALUE] ) );
            if( !empty( $transferIds ) )
            {
                $queryBuilder->orWhere( 't.id IN (:transfer_ids)' );
                $queryBuilder->setParameter( 'transfer_ids', $transferIds );
            }
        }

        $data = $queryBuilder->getQuery()->getResult();

        return array_values( $data );
    }

    /**
     * @View()
     */
    public function getTransferAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $transfer = $this->getDoctrine()
                        ->getRepository( 'Entity\Asset\Transfer' )->find( $id );
        if( $transfer !== null )
        {
            $logUtil = $this->get( 'app.util.log' );
            $logUtil->getLog( 'Entity\Asset\TransferLog', $id );
            $history = $logUtil->translateIdsToText();
            $formUtil = $this->get( 'app.util.form' );
            $formUtil->saveDataTimestamp( 'transfer' . $transfer->getId(), $transfer->getUpdatedAt() );

            $form = $this->createForm( TransferType::class, $transfer, ['allow_extra_fields' => true] );
            $transfer->setHistory( $history );
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
    public function postTransferAction( $id, Request $request )
    {
        return $this->putTransferAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putTransferAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $data = $request->request->all();
        if( $id === "null" )
        {
            $transfer = new Transfer();
        }
        else
        {
            $transfer = $em->getRepository( 'Entity\Asset\Transfer' )->find( $id );
            $formUtil = $this->get( 'app.util.form' );
            if( $formUtil->checkDataTimestamp( 'transfer' . $transfer->getId(), $transfer->getUpdatedAt() ) === false )
            {
                throw new Exception( "data.outdated", 400 );
            }
        }
        $form = $this->createForm( TransferType::class, $transfer, ['allow_extra_fields' => true] );
        try
        {
            $form->submit( $data );
            if( $form->isValid() )
            {
                $transfer = $form->getData();
                $em->persist( $transfer );
                $transferItems = $transfer->getItems();
                $transferStatus = $transfer->getStatus();

                if( $transferStatus->isLocationDestination() )
                {
                    foreach( $transferItems as $t )
                    {
                        $location = $transfer->getDestinationLocation();
                        $locationEntity = $location->getEntity();
                        $locationText = $locationEntity !== null ? $locationEntity->getName() : $location->getType()->getName();
                        $t->getAsset()->setLocation( $location )->setLocationText( $locationText );
                    }
                }
                else
                {
                    if( $transferStatus->isInTransit() )
                    {
                        $inTransit = $this->get( 'translator' )->trans( 'asset.in_transit' );
                        $queryBuilder = $em->createQueryBuilder()->select( ['l'] )
                                ->from( 'Entity\Asset\Location', 'l' )
                                ->join( 'l.type', 't' )
                                ->where( 't.name = :type' )
                                ->setParameter( 'type', $inTransit );
                        $data = $queryBuilder->getQuery()->getResult();
                        if( !empty( $data ) )
                        {
                            foreach( $transferItems as $t )
                            {
                                $t->getAsset()->setLocation( $data[0] )->setLocationText( $inTransit );
                            }
                        }
                    }
                    else
                    {
                        if( $transferStatus->isLocationUnknown() )
                        {
                            $unknown = $this->get( 'translator' )->trans( 'common.unknown' );
                            $queryBuilder = $em->createQueryBuilder()->select( ['l'] )
                                    ->from( 'Entity\Asset\Location', 'l' )
                                    ->join( 'l.type', 't' )
                                    ->where( 't.name = :type' )
                                    ->setParameter( 'type', $unknown );
                            $data = $queryBuilder->getQuery()->getResult();
                            if( !empty( $data ) )
                            {
                                foreach( $transferItems as $t )
                                {
                                    $t->getAsset()->setLocation( $data[0] )->setLocationText( $unknown );
                                }
                            }
                        }
                    }
                }
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_transfers_get_transfer', array('id' => $transfer->getId()), true // absolute
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
    public function patchTransferAction( $id, Request $request )
    {
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'Entity\Asset\Transfer' );
        $transfer = $repository->find( $id );
        if( $transfer !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $transfer->setActive( $value );
                        break;
                }

                $em->persist( $transfer );
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteTransferAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $transfer = $em->getRepository( 'Entity\Asset\Transfer' )->find( $id );
        if( $transfer !== null )
        {
            $em->getFilters()->enable( 'softdeleteable' );
            $em->remove( $transfer );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

}
