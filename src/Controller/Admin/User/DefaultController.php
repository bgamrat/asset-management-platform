<?php

Namespace App\Controller\Admin\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Admin\User\InvitationType;
use App\Form\Admin\User\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Invitation;

/**
 * Description of DefaultController
 *
 * @author bgamrat
 */
class DefaultController extends AbstractController
{

    /**
     * @Route("/admin/user/", methods={"GET"})
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_USER', null, 'Unable to access this page!' );

        $user_form = $this->createForm( UserType::class, null, [] );
        $invitation_form = $this->createForm( InvitationType::class, null, [] );

        $em = $this->getDoctrine()->getManager();
        $outstandingInvitations = $em->getRepository( 'App\Entity\Invitation' )->findAll();

        return $this->render( 'admin/user/index.html.twig', array(
                    'user_form' => $user_form->createView(),
                    'invitation_form' => $invitation_form->createView(),
                    'outstanding_invitations' => $outstandingInvitations) );
    }

    /**
     * @Route("/admin/user/invite", methods={"POST"})
     */
    public function inviteUserAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $response = new Response();
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $checkForExisting = $em->getRepository( 'App\Entity\Invitation' )->findOneByEmail( $data['email'] );
        if( $checkForExisting !== null )
        {
            throw new \Exception( 'invitation.exists' );
        }
        $user = $this->get( 'fos_user.user_manager' )->findUserBy( ['email' => $data['email']] );
        if( $user !== null )
        {
            throw new \Exception( 'user.exists' );
        }
        $form = $this->createForm( InvitationType::class, null, [] );

        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {

            $invitation = new Invitation();
            $invitation->setEmail( $data['email'] );
            $invitation->send();

            $em->persist( $invitation );
            $em->flush();
            $response->setStatusCode( 204 );

            return $response;
        }

        return $form;
    }

}
