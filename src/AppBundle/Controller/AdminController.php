<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Admin\User\InvitationType;
use AppBundle\Form\Admin\User\UserType;
use AppBundle\Form\Common\PersonType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Invitation;

class AdminController extends Controller
{

    /**
     * @Route("/admin")
     */
    public function indexAction()
    {
        return $this->render( 'admin/index.html.twig' );
    }

    /**
     * @ Route("/admin/user/")
     * @ Method("GET")
     */
    public function userAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $user_form = $this->createForm( UserType::class, null, [] );
        $person_form = $this->createForm( PersonType::class, null, [] );
        $invitation_form = $this->createForm( InvitationType::class, null, [] );

        $em = $this->getDoctrine()->getManager();
        $outstandingInvitations = $em->getRepository( 'AppBundle:Invitation' )->findAll();

        return $this->render( 'admin/user/index.html.twig', array(
                    'user_form' => $user_form->createView(),
                    'person_form' => $person_form->createView(),
                    'invitation_form' => $invitation_form->createView(),
                    'outstanding_invitations' => $outstandingInvitations,
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }

}
