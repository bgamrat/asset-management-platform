<?php

namespace AppBundle\Controller\Admin\Asset;

use AppBundle\Entity\Location;
use AppBundle\Form\Admin\Asset\LocationsType;
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
class LocationController extends Controller
{

    /**
     * @Route("/admin/asset/location")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $locations = [];
        $locations['locations'] = $em->getRepository( 'AppBundle\Entity\Asset\Location' )->findAll();
        $locationsForm = $this->createForm( LocationsType::class, $locations, [ 'action' => $this->generateUrl( 'app_admin_asset_location_save' )] );
        return $this->render( 'admin/asset/locations.html.twig', array(
                    'locations_form' => $locationsForm->createView(),
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
        $locations['locations'] = $em->getRepository( 'AppBundle\Entity\Asset\Location' )->findAll();
        $form = $this->createForm( LocationsType::class, $locations, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $locations = $form->getData();
            foreach( $locations['locations'] as $location )
            {
                $em->persist( $location );
            }

            $locations = $em->getRepository( 'AppBundle\Entity\Asset\Location' )->findAll();
            foreach( $locations as $location )
            {
                $location->setFullName();
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
