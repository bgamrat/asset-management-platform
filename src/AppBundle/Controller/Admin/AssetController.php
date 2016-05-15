<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Admin\User\InvitationType;
use AppBundle\Form\Admin\User\UserType;
use AppBundle\Form\Common\PersonType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Invitation;

/**
 * Description of UserController
 *
 * @author bgamrat
 */
class AssetController extends Controller
{
    /**
     * @Route("/admin/asset/")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        return $this->render( 'admin/asset/index.html.twig', array(
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
