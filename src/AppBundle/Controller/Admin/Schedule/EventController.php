<?php

namespace AppBundle\Controller\Admin\Schedule;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Form\Admin\Schedule\EventType;

/**
 * Description of EventController
 *
 * @author bgamrat
 */
class EventController extends Controller
{

    /**
     * @Route("/admin/schedule/event")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $form = $this->createForm( EventType::class, null, [] );

        return $this->render( 'admin/schedule/event.html.twig', array(
                    'event_form' => $form->createView(),
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }

}