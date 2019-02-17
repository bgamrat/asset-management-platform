<?php

Namespace App\Controller\Admin\Asset;

use App\Entity\Asset\LocationType;
use App\Form\Admin\Asset\LocationTypesType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of LocationController
 *
 * @author bgamrat
 */
class LocationTypeController extends AbstractController
{

    /**
     * @Route("/admin/asset/location-type", methods={"GET"})
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();

        $locationTypes = [];
        $locationTypes['types'] = $em->getRepository( 'App\Entity\Asset\LocationType' )->findAll();
        $locationTypesForm = $this->createForm( LocationTypesType::class, $locationTypes, [ 'action' => $this->generateUrl( 'app_admin_asset_locationtype_save' )
            ] );

        return $this->render( 'admin/asset/location-types.html.twig', array(
                    'location_types_form' => $locationTypesForm->createView()
                ) );
    }

    /**
     * @Route("/admin/asset/location-type/save", methods={"POST"})
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $locationTypes = [];
        $locationTypes['types'] = $em->getRepository( 'App\Entity\Asset\LocationType' )->findAll();
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
            return $this->render( 'admin/asset/location-types.html.twig', array(
                        'location_types_form' => $form->createView()) );
        }
    }

}
