<?php

Namespace App\Controller\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Admin\Asset\TrailerType;
use App\Entity\Common\CategoryQuantity;

/**
 * Description of EventController
 *
 * @author bgamrat
 */
class EventController extends AbstractController
{

    /**
     * @Route("/admin/schedule/event/{id}", methods={"GET"})
     */
    public function viewAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository( 'App\Entity\Schedule\Event' )->find( $id );

        $contracts = $event->getContracts();

        $requiresTrailers = $availableTrailers = $requiresCategoryQuantities = $availableCategoryQuantities = [];
        foreach( $contracts as $contract )
        {
            foreach( $contract->getRequiresCategoryQuantities() as $rcq )
            {
                $categoryId = $rcq->getCategory()->getId();
                if( !isset( $requiresCategoryQuantities[$categoryId] ) )
                {
                    $requiresCategoryQuantities[$categoryId] = $rcq;
                }
                else
                {
                    $requiresCategoryQuantities[$categoryId]->addQuantity( $rcq->getQuantity() );
                }
            }

            $trailers = $contract->getRequiresTrailers( true );
            $assets = $trailerNames = [];
            $trailerLocationType = $em->getRepository( 'App\Entity\Asset\LocationType' )->findOneByName( 'Trailer' );
            foreach( $trailers as $t )
            {
                $trailer = $t->getTrailer();
                $trailerId = $trailer->getId();
                $trailerName = $trailer->getName();
                $trailerAssets[$trailerName] = $em->getRepository( 'App\Entity\Asset\Asset' )->findByLocation( $trailerLocationType, $trailerId );
                $trailerNames[$trailerId] = $trailer->getName();
            }
        }

        $venueAssets = [];
        $venue = $event->getVenue();
        if( !empty( $venue ) )
        {
            $venueLocationType = $em->getRepository( 'App\Entity\Asset\LocationType' )->findOneByName( 'Venue' );
            $venueAssets[$venue->getName()] = $em->getRepository( 'App\Entity\Asset\Asset' )->findByLocation( $venueLocationType, $venue->getId() );
        }

        $satisfies = [];
        $assetCollection = [];
        $eventAssets = [];
        $locationNames = [];
        $dependencies = [];
        $requirements = array_keys( $requiresCategoryQuantities );

        $locationAssetCollection = [];
        if( !empty( $trailerAssets ) )
        {
            $locationAssetCollection = $trailerAssets;
        }
        if( !empty( $venueAssets ) )
        {
            $locationAssetCollection = array_merge( $locationAssetCollection, $venueAssets );
        }
        if( !empty( $locationAssetCollection ) )
        {
            foreach( $locationAssetCollection as $locationName => $locationAssets )
            {
                foreach( $locationAssets as $a )
                {
                    $model = $a->getModel();
                    $modelId = $model->getId();
                    $modelSatisfies = $model->getSatisfies();
                    $itemSatisfies = [];
                    if( $a->getStatus()->isAvailable() && !empty( $modelSatisfies ) )
                    {
                        $locationNames[$locationName] = true;
                        foreach( $modelSatisfies as $s )
                        {
                            $categoryId = $s->getId();
                            if( !isset( $eventAssets[$categoryId] ) )
                            {
                                $eventAssets[$categoryId] = [];
                            }
                            if( !isset( $eventAssets[$categoryId][$locationName] ) )
                            {
                                $eventAssets[$categoryId][$locationName] = 0;
                            }
                            $eventAssets[$categoryId][$locationName] ++;

                            $itemSatisfies[] = $categoryId;
                            if( !isset( $satisfies[$categoryId] ) )
                            {
                                $satisfies[$categoryId] = 0;
                            }
                            $satisfies[$categoryId] ++;
                        }
                        if( !isset( $assetCollection[$modelId] ) )
                        {
                            $assetCollection[$modelId] = 0;
                        }
                        $assetCollection[$modelId] ++;
                        if( count( array_intersect( $requirements, $itemSatisfies ) ) > 0 )
                        {

                            $modelDependencies = $this->getDependencies( $a->getModel() );
                            if( !empty( $modelDependencies ) )
                            {
                                foreach( $modelDependencies as $md )
                                {
                                    if( !isset( $dependencies[$modelId] ) )
                                    {
                                        $dependencies[$modelId] = 0;
                                    }
                                    $dependencies[$modelId] ++;
                                }
                            }
                        }
                    }
                }
            }
        }

        if( !empty( $assetCollection ) )
        {
            foreach( $assetCollection as $modelId => $ac )
            {
                if( isset( $dependencies[$modelId] ) )
                {
                    $assetCollection[$modelId] -= $dependencies[$modelId];
                }
            }
        }

        $assetBalance = [];

        $categoryQuantities = $event->getCategoryQuantities();
        foreach( $categoryQuantities as $cq )
        {
            $cId = $cq->getCategory()->getId();
            if( !isset( $assetBalance[$cId] ) )
            {
                $assetBalance[$cId] = clone($cq);
            }
        }

        foreach( $requiresCategoryQuantities as $categoryId => $rcq )
        {
            if( !isset( $assetBalance[$categoryId] ) )
            {
                $assetBalance[$categoryId] = clone($rcq);
            }
            if( isset( $satisfies[$categoryId] ) )
            {
                $assetBalance[$categoryId]->subtractQuantity( $satisfies[$categoryId] );
            }
        }

        $rentalEquipment = $event->getRentals();
        $rentals = [];
        foreach( $rentalEquipment as $re )
        {
            $cId = $re->getCategory()->getId();
            if( !isset( $assetBalance[$cId] ) )
            {
                $cq = new CategoryQuantity();
                $cq->setCategory( $re->getCategory() );
                $cq->setQuantity( 0 );
                $assetBalance[$cId] = $cq;
            }
            if( !isset( $rentals[$cId] ) )
            {
                $rentals[$cId] = 0;
            }
            $assetBalance[$cId]->subtractQuantity( $re->getQuantity() );
            $rentals[$cId] += $re->getQuantity();
        }

        $clientEquipment = $event->getClientEquipment();
        $clientProvided = [];
        foreach( $clientEquipment as $ce )
        {
            $cId = $ce->getCategory()->getId();
            if( !isset( $assetBalance[$cId] ) )
            {
                $cq = new CategoryQuantity();
                $cq->setCategory( $ce->getCategory() );
                $cq->setQuantity( 0 );
                $assetBalance[$cId] = $cq;
            }
            if( !isset( $clientProvided[$cId] ) )
            {
                $clientProvided[$cId] = 0;
            }
            $assetBalance[$cId]->subtractQuantity( $ce->getQuantity() );
            $clientProvided[$cId] += $ce->getQuantity();
        }

        $deficits = [];
        foreach( $assetBalance as $cId => $ab )
        {
            if( $ab->getQuantity() > 0 )
            {
                if( !isset( $deficits[$cId] ) )
                {
                    $deficits[$cId] = 0;
                }
                $deficits[$cId] += $ab->getQuantity();
            }
        }

        if( count( $deficits ) > 0 )
        {
            $categoryDeficits = array_keys( $deficits );
            $sets = $em->getRepository( 'App\Entity\Asset\Set' )->findBySatisfies( $categoryDeficits );
            if( !empty( $sets ) )
            {
                foreach( $sets as $s )
                {
                    $categoryId = $s['category_id'];
                    $set = $s[0];
                    $models = $set->getModels();
                    $missingAssets = false;
                    foreach( $models as $m )
                    {
                        $id = $m->getId();
                        if( !isset( $assetCollection[$id] ) || $assetCollection[$id] === 0 )
                        {
                            $missingAssets = true;
                        }
                    }
                    if( $missingAssets === false )
                    {
                        foreach( $models as $m )
                        {
                            $id = $m->getId();
                            $assetCollection[$id] --;
                        }
                        $assetBalance[$categoryId]->subtractQuantity( 1 );
                        $deficits[$categoryId] --;
                        if( $deficits[$categoryId] <= 0 )
                        {
                            unset( $deficits[$categoryId] );
                            if( empty( $deficits ) )
                            {
                                break;
                            }
                        }
                    }
                }
            }
        }

        $columns = ['t.id', 't.updatedAt', 't.tracking_number', 'c.name AS carrier', 'c.tracking_url', 'cs.name AS carrier_service', 's.name AS status', 't.source_location_text', 't.destination_location_text', 'tb.amount'];
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'App\Entity\Asset\Transfer', 't' )
                ->join( 't.status', 's' )
                ->leftJoin( 't.carrier', 'c' )
                ->innerJoin( 't.carrier_service', 'cs' )
                ->join( 't.bill_tos', 'tb' )
                ->where( 'tb.event = :event_id' )
                ->setParameter( 'event_id', $id )
                ->orderBy( 't.updatedAt' );
        $transfers = $queryBuilder->distinct()->getQuery()->getResult();

        if( !empty( $transfers ) )
        {
            $transferIds = array_column( $transfers, 'id' );
            $transferIndex = array_flip( $transferIds );
            $columns = ['t.id', 'a.id AS asset_id', 'b.barcode', 'br.name AS brand', 'm.name AS model'];
            $queryBuilder = $em->createQueryBuilder()->select( $columns )
                    ->from( 'App\Entity\Asset\Transfer', 't' )
                    ->leftJoin( 't.items', 'ti' )
                    ->leftJoin( 'ti.asset', 'a' )
                    ->leftJoin( 'a.model', 'm' )
                    ->leftJoin( 'm.brand', 'br' )
                    ->leftJoin( 'a.barcodes', 'b' )
                    ->where( 't.id IN (:transfer_ids)' )
                    ->setParameter( 'transfer_ids', $transferIds )
                    ->orderBy( 't.updatedAt,b.barcode' );
            $transferItems = $queryBuilder->getQuery()->getResult();
            if( !empty( $transferItems ) )
            {
                foreach( $transferItems as $ti )
                {
                    $index = $transferIndex[$ti['id']];
                    if( !isset( $transfers[$index]['items'] ) )
                    {
                        $transfers[$index]['items'] = [];
                    }
                    $transfers[$index]['items'][] = $ti;
                }
            }
        }

        return $this->render( 'common/event.html.twig', [
                    'event' => $event,
                    'event_assets' => $eventAssets,
                    'location_names' => $locationNames,
                    'asset_balance' => $assetBalance,
                    'client_provided' => $clientProvided,
                    'rentals' => $rentals,
                    'transfers' => $transfers,
                    'no_hide' => true,
                    'omit_menu' => true]
        );
    }

    private function getDependencies( $model )
    {
        $result = $model->getRequires();
        if( !empty( $requires ) )
        {
            foreach( $requires as $item )
            {
                $result = $this->getDependencies( $item );
            }
        }
        return $result;
    }

}