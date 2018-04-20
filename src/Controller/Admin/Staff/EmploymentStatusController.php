<?php

Namespace App\Controller\Admin\Staff;

use App\Entity\Staff\EmploymentStatus;
use App\Form\Admin\Staff\EmploymentStatusesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of EmploymentStatusTypeController
 *
 * @author bgamrat
 */
class EmploymentStatusController extends Controller
{

    /**
     * @Route("/admin/staff/employment-status-type")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $employmentStatuses = [];
        $employmentStatuses['statuses'] = $em->getRepository( 'App\Entity\Staff\EmploymentStatus' )->findAll();
        $employmentStatusesForm = $this->createForm( EmploymentStatusesType::class, $employmentStatuses,
                [ 'action' => $this->generateUrl( 'app_admin_staff_employmentstatus_save' )] );
        return $this->render( 'admin/staff/employment-status.html.twig', array(
                    'employment_statuses_form' => $employmentStatusesForm->createView())
                 );
    }

    /**
     * @Route("/admin/staff/employment-status/save")
     * @Method("POST")
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $employmentStatuses = [];
        $employmentStatuses['statuses'] = $em->getRepository( 'App\Entity\Staff\EmploymentStatus' )->findAll();
        $ids = [];
        foreach ($employmentStatuses['statuses'] as $es) {
            $ids[$es->getId()] = $es;
        }
        $form = $this->createForm( EmploymentStatusesType::class, $employmentStatuses, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $employmentStatuses = $form->getData();
            foreach( $employmentStatuses['statuses'] as $status )
            {
                if ($status !== null) {
                    unset($ids[$status->getId()]);
                    $em->persist( $status );
                }
            }
            foreach ($ids as $es) {
                $em->remove($es);
            }
            $em->flush();
            $this->addFlash(
                    'notice', 'common.success' );
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_staff_employmentstatus_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
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
            return $this->render( 'admin/staff/role-types.html.twig', array(
                        'employment_statuses_form' => $form->createView()
                    ) );
        }
    }

}
