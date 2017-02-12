<?php

namespace AppBundle\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Admin\Asset\TrailerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Description of AssetController
 *
 * @author bgamrat
 */
class TrailerController extends Controller
{

    /**
     * @Route("/admin/asset/trailer")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $trailerForm = $this->createForm( TrailerType::class, null, [] );

        return $this->render( 'admin/asset/trailers.html.twig', array(
                    'trailer_form' => $trailerForm->createView()
                ) );
    }

    /**
     * @Route("/admin/asset/trailer/{name}")
     * @Method("GET")
     */
    public function viewAction( $name )
    {
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
                        $queryBuilder->expr()->eq( 'lt.entity', "'trailer'" ), $queryBuilder->expr()->eq( 'l.entity', '?1' )
        ) );
        $queryBuilder->setParameter( 1, $trailer->getId() );
        $equipment = $queryBuilder->getQuery()->getResult();

        return $this->render( 'admin/client/trailer-equipment-by-category.html.twig', array(
                    'trailer' => $trailer,
                    'equipment' => $equipment,
                    'no_hide' => true,
                    'omit_menu' => true)
        );
    }

}
