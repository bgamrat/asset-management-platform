<?php

Namespace App\Controller\Admin\Schedule;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Form\Admin\Schedule\EventType;
use App\Form\Common\PersonType;

/**
 * Description of EventController
 *
 * @author bgamrat
 */
class EventController extends AbstractController
{

    /**
     * @Route("/admin/schedule/event", methods={"GET"})
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $eventForm = $this->createForm( EventType::class, null, [] );

        return $this->render( 'admin/schedule/event.html.twig', array(
                    'event_form' => $eventForm->createView()
                ) );
    }

}
