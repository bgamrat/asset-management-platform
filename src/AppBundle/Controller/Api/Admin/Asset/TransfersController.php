<?php

namespace AppBundle\Controller\Api\Admin\Asset;

use AppBundle\Util\DStore;
use AppBundle\Entity\Asset\Transfer;
use AppBundle\Entity\Asset\Trailer;
use AppBundle\Entity\Common\Person;
use AppBundle\Entity\Asset\Location;
use AppBundle\Form\Admin\Asset\TransferType;
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
                $sortField = 's.status';
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
            $columns[] = 'i.deletedAt AS deleted_at';
        }
        $transferIds = [];
        if( !empty( $dstore['filter'][DStore::VALUE] ) )
        {
            $assetData = $em->getRepository( 'AppBundle\Entity\Asset\Asset' )->findByBarcode( $dstore['filter'][DStore::VALUE] );
            if( !empty( $assetData ) )
            {
                $assetIds = [];
                foreach( $assetData as $a )
                {
                    $assetIds[] = $a->getId();
                }
                $queryBuilder = $em->createQueryBuilder()->select( 't.id' )
                        ->from( 'AppBundle\Entity\Asset\Transfer', 't' )
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

        $columns = ['t.id', 't.instructions', 's.name AS status_text',
            "CONCAT(CONCAT(to.firstname,' '),to.lastname) AS to_text",
            "CONCAT(CONCAT(fm.firstname,' '),fm.lastname) AS from_text",
            'b.barcode'];
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle\Entity\Asset\Transfer', 't' )
                ->join( 't.status', 's' )
                ->leftJoin( 't.to', 'to' )
                ->leftJoin( 't.from', 'fm' )
                ->leftJoin( 't.items', 'ti' )
                ->join( 'ti.asset', 'a' )
                ->join( 'a.barcodes', 'b' )
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
                        ->getRepository( 'AppBundle\Entity\Asset\Transfer' )->find( $id );
        if( $transfer !== null )
        {
            $data = [
                'id' => $id,
                'status' => $transfer->getStatus(),
                'items' => $transfer->getItems(),
                'from' => $transfer->getFrom(),
                'source_location' => $transfer->getSourceLocation(),
                'to' => $transfer->getTo(),
                'destination_location' => $transfer->getDestinationLocation(),
                'carrier' => $transfer->getCarrier(),
                'carrier_service' => $transfer->getCarrierService(),
                'tracking_number' => $transfer->getTrackingNumber(),
                'bill_to' => $transfer->getBillTos(),
                'cost' => $transfer->getCost(),
                'instructions' => $transfer->getInstructions(),
                'cost' => $transfer->getCost(),
                'created' => $transfer->getCreated()->format( 'Y-m-d H:i:s' ),
                'updated' => $transfer->getUpdated()->format( 'Y-m-d H:i:s' )
            ];

            $logUtil = $this->get( 'app.util.log' );
            $logUtil->getLog( 'AppBundle\Entity\Asset\TransferLog', $id );
            $data['history'] = $logUtil->translateIdsToText();
            $formUtil = $this->get( 'app.util.form' );
            $formUtil->saveDataTimestamp( 'transfer' . $transfer->getId(), $transfer->getUpdated() );
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
            $transfer = $em->getRepository( 'AppBundle\Entity\Asset\Transfer' )->find( $id );
            $formUtil = $this->get( 'app.util.form' );
            if( $formUtil->checkDataTimestamp( 'transfer' . $transfer->getId(), $transfer->getUpdated() ) === false )
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
        $repository = $em->getRepository( 'AppBundle\Entity\Asset\Transfer' );
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
        $transfer = $em->getRepository( 'AppBundle\Entity\Asset\Transfer' )->find( $id );
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
