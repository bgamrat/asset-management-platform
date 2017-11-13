<?php

namespace AppBundle\Controller\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Admin\Asset\TrailerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Description of TrailerController
 *
 * @author bgamrat
 */
class TrailerController extends Controller
{

    /**
     * @Route("/trailers/", name="trailers")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $repository = $this->getDoctrine()
                ->getRepository( '\AppBundle\Entity\Asset\Trailer' );
        $trailers = $repository->findAll();
        $equipment = [];
        foreach( $trailers as $trailer )
        {
            $equipment[$trailer->getName()] = $this->getTrailerEquipmentModelCounts( $trailer );
        }

        return $this->render( 'public/trailers/index.html.twig', array(
                    'trailers' => $trailers,
                    'equipment' => $equipment
                ) );
    }

    /**
     * @Route("/admin/asset/trailer/{name}", name="app_admin_asset_trailer_view", defaults={"name": "index"})
     * @Method("GET")
     */
    public function viewAction( $name )
    {
        if( empty( $name ) || $name === 'index' )
        {
            $this->redirect( $this->generateUrl( 'trailers' ) );
        }

        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $trailer = $em->getRepository( 'AppBundle\Entity\Asset\Trailer' )->findOneByName( $name );
        return $this->render( 'admin/asset/trailer.html.twig', array(
                    'trailer' => $trailer
                ) );
    }

    /**
     * @Route("/admin/trailer/{name}/equipment-by-category")
     * @Method("GET")
     */
    public function viewTrailerEquipmentAction( $name )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $trailer = $em->getRepository( 'AppBundle\Entity\Asset\Trailer' )->findOneByName( $name );
        return $this->render( 'common/trailer-equipment-by-category.html.twig', array(
                    'trailer' => $trailer,
                    'equipment' => $this->getTrailerEquipment( $trailer ),
                    'model_counts' => $this->getTrailerEquipmentModelCounts( $trailer ),
                    'no_hide' => true,
                    'omit_menu' => true)
        );
    }

    private function getTrailerEquipment( $trailer )
    {
        $em = $this->getDoctrine()->getManager();
        $trailerLocationType = $em->getRepository( 'AppBundle\Entity\Asset\LocationType' )->findOneByName( 'Trailer' );
        $assets = $em->getRepository( 'AppBundle\Entity\Asset\Asset' )->findByLocation( $trailerLocationType, $trailer->getId() );
        $lastModelId = null;
        foreach( $assets as $i => $a )
        {
            if( $a->getModel()->getId() === $lastModelId )
            {
                unset( $assets[$i] );
            }
            else
            {
                $lastModelId = $a->getModel()->getId();
            }
        }
        return $assets;
    }

    private function getTrailerEquipmentModelCounts( $trailer )
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder()->select( 'm.id', 'm.name', 'COUNT(m.id) AS quantity' )
                ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                ->join( 'a.model', 'm' )
                ->innerJoin( 'a.location', 'l' )
                ->innerJoin( 'l.type', 'lt' )
                ->groupBy( 'm.id' )
                ->orderBy( 'm.name' );
        $queryBuilder->where(
                $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq( 'lt.entity', "'trailer'" ), $queryBuilder->expr()->eq( 'l.entity', '?1' )
        ) );
        $queryBuilder->setParameter( 1, $trailer->getId() );
        return array_column( $queryBuilder->getQuery()->getResult(), 'quantity', 'name' );
    }

}
