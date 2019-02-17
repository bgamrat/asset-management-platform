<?php

Namespace App\Controller\Admin\Schedule;

use App\Entity\Schedule\TimeSpanType;
use App\Form\Admin\Schedule\TimeSpanTypesType;
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
class TimeSpanTypeController extends AbstractController
{

    /**
     * @Route("/admin/schedule/time-span-type", methods={"GET"})
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $time_span_types = [];
        $time_span_types['types'] = $em->getRepository( 'App\Entity\Schedule\TimeSpanType' )->findAll();

        $time_span_typesForm = $this->createForm( TimeSpanTypesType::class, $time_span_types, [ 'action' => $this->generateUrl( 'app_admin_schedule_timespantype_save' )] );

        return $this->render( 'admin/schedule/time-span-types.html.twig', array(
                    'types_form' => $time_span_typesForm->createView()
                ) );
    }

    /**
     * @Route("/admin/schedule/time-span-type/save", methods={"POST"})
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $time_span_types = [];
        $time_span_types['types'] = $em->getRepository( 'App\Entity\Schedule\TimeSpanType' )->findAll();
        $form = $this->createForm( TimeSpanTypesType::class, $time_span_types, ['allow_extra_fields' => true] );
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
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_schedule_timespantype_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
            $response->prepare( $request );

            return $response->send();
        }
        else
        {
            return $this->render( 'admin/schedule/time-span-types.html.twig', array(
                        'time_span_types_form' => $form->createView()
                    ) );
        }
    }

}
