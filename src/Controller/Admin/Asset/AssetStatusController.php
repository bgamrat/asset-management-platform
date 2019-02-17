<?php

Namespace App\Controller\Admin\Asset;

use App\Entity\Asset\AssetStatus;
use App\Form\Admin\Asset\AssetStatusesType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of StatusController
 *
 * @author bgamrat
 */
class AssetStatusController extends AbstractController
{

    /**
     * @Route("/admin/asset/asset-status", methods={"GET"})
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $statuses = [];
        $statuses['statuses'] = $em->getRepository( 'App\Entity\Asset\AssetStatus' )->findAll();

        $statusesForm = $this->createForm( AssetStatusesType::class, $statuses, [ 'action' => $this->generateUrl( 'app_admin_asset_assetstatus_save' )] );

        return $this->render( 'admin/asset/asset-statuses.html.twig', array(
                    'statuses_form' => $statusesForm->createView(),
                    'no_settings' => true
                ) );
    }

    /**
     * @Route("/admin/asset/asset-status/save", methods={"POST"})
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $statuses = [];
        $statuses['statuses'] = $em->getRepository( 'App\Entity\Asset\AssetStatus' )->findAll();
        $form = $this->createForm( AssetStatusesType::class, $statuses, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $statuses = $form->getData();
            foreach( $statuses['statuses'] as $status )
            {
                $em->persist( $status );
            }
            $em->flush();
            $this->addFlash(
                    'notice', 'common.success' );
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_asset_assetstatus_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
            $response->prepare( $request );

            return $response->send();
        }
        else
        {
            return $this->render( 'admin/asset/asset-statuses.html.twig', array(
                        'statuses_form' => $form->createView()) );
        }
    }

}
