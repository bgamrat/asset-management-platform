<?php

namespace Common\AdminBundle\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\AdminBundle\Form\User\InvitationType;
use Common\AdminBundle\Form\User\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Common\AppBundle\Entity\Invitation;

/**
 * Description of UserController
 *
 * @author bgamrat
 */
class UserController extends Controller
{
    /**
     * @Route("/admin/user/")
     * @Method("GET")
     */
    public function userAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $user_form = $this->createForm( UserType::class, null, [] );
        $invitation_form = $this->createForm( InvitationType::class, null, [] );
        
        $em = $this->getDoctrine()->getManager();
        $outstandingInvitations = $em->getRepository('AppBundle:Invitation')->findAll();

        return $this->render( 'admin/user/index.html.twig', array(
                    'user_form' => $user_form->createView(),
                    'invitation_form' => $invitation_form->createView(),
                    'outstanding_invitations' => $outstandingInvitations,
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }

    /**
     * @Route("/admin/user/invite")
     * @Method("POST")
     */
    public function inviteUserAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $response = new Response();
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $form = $this->createForm( InvitationType::class, null, [] );
        try
        {
            $formValid = $formProcessor->validateFormData( $form, $data );
            
            $em = $this->getDoctrine()->getManager();
            $checkForExisting = $em->getRepository('AppBundle:Invitation')->findOneByEmail($data['email']);
            if ($checkForExisting !== null) {
                throw new \Exception('invitation.exists');
            }
            $user = $this->get( 'fos_user.user_manager' )->findUserBy( ['email' => $data['email']] );
            if ($user !== null) {
                throw new \Exception('user.exists');
            }
            
            $invitation = new Invitation();
            $invitation->setEmail( $data['email'] );
            $invitation->send();

            $em->persist( $invitation );
            $em->flush();
            $response->setStatusCode( 204 );
        }
        catch( \Exception $e )
        {
            $response->setStatusCode( 400 );
            $response->setContent( json_encode(
                            ['message' => 'errors', 'errors' => $e->getMessage()]
            ) );
        }
        return $response;
    }


}
