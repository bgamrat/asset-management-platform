<?php

namespace AppBundle\Controller\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Admin\Asset\TrailerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Description of EventController
 *
 * @author bgamrat
 */
class EventController extends Controller
{

    /**
     * @Route("/admin/schedule/event/{id}")
     * @Method("GET")
     */
    public function viewAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository( 'AppBundle\Entity\Schedule\Event' )->find( $id );

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
            $trailerLocationType = $em->getRepository( 'AppBundle\Entity\Asset\LocationType' )->findOneByName( 'Trailer' );
            foreach( $trailers as $t )
            {
                $trailer = $t->getTrailer();
                $trailerId = $trailer->getId();
                $trailerName = $trailer->getName();
                $trailerAssets[$trailerName] = $em->getRepository( 'AppBundle\Entity\Asset\Asset' )->findByLocation( $trailerLocationType, $trailerId );
                $trailerNames[$trailerId] = $trailer->getName();
            }
        }

        $venueAssets = [];
        $venue = $event->getVenue();
        if( !empty( $venue ) )
        {
            $venueLocationType = $em->getRepository( 'AppBundle\Entity\Asset\LocationType' )->findOneByName( 'Venue' );
            $venueAssets[$venue->getName()] = $em->getRepository( 'AppBundle\Entity\Asset\Asset' )->findByLocation( $venueLocationType, $venue->getId() );
        }

        $satisfies = [];
        $assetCollection = [];
        $eventAssets = [];
        $locationNames = [];
        $dependencies = [];
        $requirements = array_keys( $requiresCategoryQuantities );
        $locationAssetCollection = array_merge( $trailerAssets, $venueAssets );
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

                    if( count( array_intersect( $requirements, $itemSatisfies ) ) > 0 )
                    {
                        if( !isset( $assetCollection[$modelId] ) )
                        {
                            $assetCollection[$modelId] = 0;
                        }
                        $assetCollection[$modelId] ++;
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

        foreach( $assetCollection as $modelId => $ac )
        {
            if( isset( $dependencies[$modelId] ) )
            {
                $assetCollection[$modelId] -= $dependencies[$modelId];
            }
        }

        $assetBalance = [];
        foreach( $requiresCategoryQuantities as $categoryId => $rcq )
        {
            $assetBalance[$categoryId] = clone($rcq);
            if( isset( $satisfies[$categoryId] ) )
            {
                $assetBalance[$categoryId]->subtractQuantity( $satisfies[$categoryId] );
            }
        }

        return $this->render( 'common/event-view.html.twig', [
                    'event' => $event,
                    'event_assets' => $eventAssets,
                    'location_names' => $locationNames,
                    'asset_balance' => $assetBalance,
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
