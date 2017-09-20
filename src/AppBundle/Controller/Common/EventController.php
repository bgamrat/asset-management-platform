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

        foreach( $assets as $trailerId => $trailerAssets )
        {
            foreach( $trailerAssets as $a )
            {
                $categoryId = $a->getModel()->getCategory()->getId();
                if( isset( $requiresCategoryQuantities[$categoryId] ) )
                {
                    $requiresCategoryQuantities[$categoryId]
                            ->subtractQuantity( 1 );
                }
                $satisfies = $a->getModel()->getSatisfies();
                dump($satisfies);

                
            }
        }
        dump( $assets, $requiresCategoryQuantities );
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

}
