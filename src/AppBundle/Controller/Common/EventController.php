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
     * @Route("/admin/schedule/event/{id}/equipment-by-category")
     * @Method("GET")
     */
    public function viewEventEquipmentAction( $id )
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
            $locationType = $em->getRepository( 'AppBundle\Entity\Asset\LocationType' )->findOneByName( 'Trailer' );
            foreach( $trailers as $t )
            {
                $trailer = $t->getTrailer();
                $trailerId = $trailer->getId();
                $assets[$trailerId] = $em->getRepository( 'AppBundle\Entity\Asset\Asset' )->findByLocation( $locationType, $trailerId );
                $trailerNames[$trailerId] = $trailer->getName();
            }
        }

        $satisfies = [];
        $assetCollection = [];
        $dependencies = [];
        foreach( $assets as $trailerId => $trailerAssets )
        {
            foreach( $trailerAssets as $a )
            {
                $model = $a->getModel();
                $modelId = $model->getId();
                $modelSatisfies = $model->getSatisfies();
                if( !empty( $modelSatisfies ) )
                {
                    foreach( $modelSatisfies as $s )
                    {
                        $categoryId = $s->getId();
                        if( !isset( $satisfies[$categoryId] ) )
                        {
                            $satisfies[$categoryId] = 0;
                        }
                        $satisfies[$categoryId] ++;
                    }
                }
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
            if( isset( $satisfies[$categoryId] ) )
            {
                $assetBalance[$categoryId] = $satisfies[$categoryId] - $rcq->getQuantity();
            }
            else
            {
                $assetBalance[$categoryId] = - $rcq->getQuantity();
            }
        }

        dump( $assetCollection, $dependencies, $satisfies, $requiresCategoryQuantities, $assetBalance );
        die;

        return $this->render( 'common/event-equipment-by-category.html.twig', array(
                    'event' => $event,
                    'equipment' => $this->getEventEquipment( $event ),
                    'no_hide' => true,
                    'omit_menu' => true)
        );
    }

    private function getEventEquipment( $event )
    {
        $em = $this->getDoctrine()->getManager();
        $trailers = $event->getTrailers();
        $trailerIds = [];
        foreach( $trailers as $t )
        {
            $trailerIds[] = $t->getId();
        }
        $queryBuilder = $em->createQueryBuilder()->select( 'c.fullName', 'COUNT(c.id) AS quantity' )
                ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                ->join( 'a.model', 'm' )
                ->join( 'm.category', 'c' )
                ->innerJoin( 'a.location', 'l' )
                ->innerJoin( 'l.type', 'lt' )
                ->groupBy( 'c.id' )
                ->orderBy( 'c.fullName' );
        $queryBuilder->where(
                $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq( 'lt.entity', "'trailer'" ), $queryBuilder->expr()->in( 'l.entity', '?1' )
        ) );
        $queryBuilder->setParameter( 1, $trailerIds );
        return $queryBuilder->getQuery()->getResult();
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
