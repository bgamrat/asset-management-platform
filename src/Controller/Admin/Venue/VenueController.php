<?php

Namespace App\Controller\Admin\Venue;

use App\Form\Admin\Venue\VenueType;
use App\Form\Admin\Venue\ContractType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of AssetController
 *
 * @author bgamrat
 */
class VenueController extends AbstractController
{

    /**
     * @Route("/admin/venue/venue", methods={"GET"})
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $form = $this->createForm( VenueType::class, null, [] );

        $id = $request->get( 'id' );

        return $this->render( 'admin/venue/venue.html.twig', array(
                    'venue_form' => $form->createView(), 'id' => $id) );
    }

    /**
     * @Route("/admin/venue/{id}/equipment", methods={"GET"})
     */
    public function viewVenueEquipmentAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $venue = $em->getRepository( 'App\Entity\Venue\Venue' )->find( $id );
        $locationType = $em->getRepository( 'App\Entity\Asset\LocationType' )->findBy( ['name' => 'Venue'] )[0];
        $venueEquipment = $em->getRepository( 'App\Entity\Asset\Asset' )->findByLocation( $locationType->getId(), $id );

        return $this->render( 'admin/venue/venue-equipment.html.twig', array(
                    'venue' => $venue,
                    'venue_equipment' => $venueEquipment,
                    'no_hide' => true,
                    'omit_menu' => true)
        );
    }

}
