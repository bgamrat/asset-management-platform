<?php

namespace AppBundle\Controller\Admin\Asset;

use AppBundle\Entity\AssetStatus;
use AppBundle\Form\Admin\Asset\AssetStatusesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of StatusController
 *
 * @author bgamrat
 */
class StatusController extends Controller
{

    /**
     * @Route("/admin/asset/status")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $statuses = [];
        $statuses['statuses'] = $em->getRepository( 'AppBundle:AssetStatus' )->findAll();

        $statusesForm = $this->createForm( AssetStatusesType::class, $statuses, [ 'action' => $this->generateUrl( 'app_admin_asset_status_save' )] );

        return $this->render( 'admin/asset/statuses.html.twig', array(
                    'statuses_form' => $statusesForm->createView(),
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }

    /**
     * @Route("/admin/asset/status/save")
     * @Method("POST")
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $statuses = [];
        $statuses['statuses'] = $em->getRepository( 'AppBundle:AssetStatus' )->findAll();
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
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_asset_status_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
            $response->prepare( $request );

            return $response->send();
        }
        else
        {
            return $this->render( 'admin/asset/statuses.html.twig', array(
                        'statuses_form' => $form->createView(),
                        'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                    ) );
        }
    }

}
