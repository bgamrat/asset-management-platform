<?php

namespace AppBundle\Controller\Admin\Asset;

use AppBundle\Entity\LocationType;
use AppBundle\Form\Admin\Asset\LocationTypesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of LocationController
 *
 * @author bgamrat
 */
class LocationTypeController extends Controller
{

    /**
     * @Route("/admin/asset/location-type")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $locations = [];
        $locationTypes['types'] = $em->getRepository( 'AppBundle:LocationType' )->findAll();

        $locationTypesForm = $this->createForm( LocationTypesType::class, $locationTypes, [ 'action' => $this->generateUrl( 'app_admin_asset_locationtype_save' )] );

        return $this->render( 'admin/asset/location_types.html.twig', array(
                    'location_types_form' => $locationTypesForm->createView(),
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }

    /**
     * @Route("/admin/asset/location/save")
     * @Method("POST")
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $locations = [];
        $locations['locations'] = $em->getRepository( 'AppBundle:Location' )->findAll();
        $form = $this->createForm( LocationsType::class, $locations, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $locations = $form->getData();
            foreach( $locations['locations'] as $location )
            {
                $em->persist( $location );
            }
            $em->flush();
            $this->addFlash(
                    'notice', 'common.success' );
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_asset_location_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
            $response->prepare( $request );

            return $response->send();
        }
        else
        {
            $errorMessages = [];
            $formData = $form->all();
            foreach( $formData as $name => $item )
            {
                if( !$item->isValid() )
                {
                    $errorMessages[] = $name . ' - ' . $item->getErrors( true );
                }
            }
            return $this->render( 'admin/asset/locations.html.twig', array(
                        'locations_form' => $form->createView(),
                        'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                    ) );
        }
    }

}
