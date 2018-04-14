<?php

Namespace App\Controller\Admin\Schedule;

use Form\Admin\Schedule\EventRoleTypesType;
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
class EventRoleTypeController extends Controller
{

    /**
     * @Route("/admin/schedule/event-role-type")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $event_role_types = [];
        $event_role_types['types'] = $em->getRepository( 'Entity\Schedule\EventRoleType' )->findAll();

        $event_role_typesForm = $this->createForm( EventRoleTypesType::class, $event_role_types, [ 'action' => $this->generateUrl( 'app_admin_schedule_eventroletype_save' )] );

        return $this->render( 'admin/schedule/event-role-types.html.twig', array(
                    'types_form' => $event_role_typesForm->createView()
                ) );
    }

    /**
     * @Route("/admin/schedule/event-role-type/save")
     * @Method("POST")
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $event_role_types = [];
        $event_role_types['types'] = $em->getRepository( 'Entity\Schedule\EventRoleType' )->findAll();
        $form = $this->createForm( EventRoleTypesType::class, $event_role_types, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $time_span_types = $form->getData();
            foreach( $time_span_types['types'] as $time_span_type )
            {
                $em->persist( $time_span_type );
            }
            $em->flush();
            $this->addFlash(
                    'notice', 'common.success' );
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_schedule_eventroletype_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
            $response->prepare( $request );

            return $response->send();
        }
        else
        {
            return $this->render( 'admin/schedule/event-role-types.html.twig', array(
                        'time_span_types_form' => $form->createView()
                    ) );
        }
    }

}
