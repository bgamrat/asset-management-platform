<?php

Namespace App\Controller\Admin\Asset;

use App\Entity\Asset\TransferStatus;
use App\Form\Admin\Asset\TransferStatusesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of TransferStatusController
 *
 * @author bgamrat
 */
class TransferStatusController extends Controller
{

    /**
     * @Route("/admin/asset/transfer-status")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $statuses = [];
        $statuses['statuses'] = $em->getRepository( 'App\Entity\Asset\TransferStatus' )->findAll();

        $statusesForm = $this->createForm( TransferStatusesType::class, $statuses, [ 'action' => $this->generateUrl( 'app_admin_asset_transferstatus_save' )] );

        return $this->render( 'admin/asset/transfer-statuses.html.twig', array(
                    'statuses_form' => $statusesForm->createView()
                ) );
    }

    /**
     * @Route("/admin/asset/transfer-status/save")
     * @Method("POST")
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $statuses = [];
        $statuses['statuses'] = $em->getRepository( 'App\Entity\Asset\TransferStatus' )->findAll();
        $form = $this->createForm( TransferStatusesType::class, $statuses, ['allow_extra_fields' => true] );
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
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_asset_transferstatus_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
            $response->prepare( $request );

            return $response->send();
        }
        else
        {
            return $this->render( 'admin/asset/transfer-statuses.html.twig', array(
                        'statuses_form' => $form->createView()) );
        }
    }

}
