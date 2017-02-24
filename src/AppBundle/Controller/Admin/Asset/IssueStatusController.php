<?php

namespace AppBundle\Controller\Admin\Asset;

use AppBundle\Entity\Asset\IssueStatus;
use AppBundle\Form\Admin\Asset\IssueStatusesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of IssueStatusController
 *
 * @author bgamrat
 */
class IssueStatusController extends Controller
{

    /**
     * @Route("/admin/asset/issue-status")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $issueStatuses = [];
        $issueStatuses['statuses'] = $em->getRepository( 'AppBundle\Entity\Asset\IssueStatus' )->findAll();
        $issueStatusesForm = $this->createForm( IssueStatusesType::class, $issueStatuses, [ 'action' => $this->generateUrl( 'app_admin_asset_issuestatus_save' )] );
        return $this->render( 'admin/asset/issue-statuses.html.twig', array(
                    'issue_statuses_form' => $issueStatusesForm->createView())
                 );
    }

    /**
     * @Route("/admin/asset/issue-status/save")
     * @Method("POST")
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $issueStatuses = [];
        $issueStatuses['statuses'] = $em->getRepository( 'AppBundle\Entity\Asset\IssueStatus' )->findAll();
        $form = $this->createForm( IssueStatusesType::class, $issueStatuses, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $issueStatuses = $form->getData();
            foreach( $issueStatuses['statuses'] as $status )
            {
                $em->persist( $status );
            }
            $em->flush();
            $this->addFlash(
                    'notice', 'common.success' );
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_asset_issuestatus_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
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
            return $this->render( 'admin/asset/issue-statuses.html.twig', array(
                        'issue_statuses_form' => $form->createView()
                    ) );
        }
    }

}
