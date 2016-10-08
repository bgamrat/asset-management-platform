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
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $location_types = [];
        $locationTypes['types'] = $em->getRepository( 'AppBundle:LocationType' )->findAll();
        $locationTypesForm = $this->createForm( LocationTypesType::class, $locationTypes, [ 'action' => $this->generateUrl( 'app_admin_asset_locationtype_save' )] );

        return $this->render( 'admin/asset/location_types.html.twig', array(
                    'location_types_form' => $locationTypesForm->createView(),
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }

    /**
     * @Route("/admin/asset/location-type/save")
     * @Method("POST")
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $locationTypes = [];
        $locationTypes['types'] = $em->getRepository( 'AppBundle:LocationType' )->findAll();
        $form = $this->createForm( LocationTypesType::class, $locationTypes, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $locationTypes = $form->getData();
            foreach( $locationTypes['types'] as $type )
            {
                $em->persist( $type );
            }
            $em->flush();
            $this->addFlash(
                    'notice', 'common.success' );
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_asset_locationtype_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
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
            return $this->render( 'admin/asset/location_types.html.twig', array(
                        'location_types_form' => $form->createView(),
                        'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                    ) );
        }
    }

}
