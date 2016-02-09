<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use AppBundle\Form\Admin\User\UserType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;

class UsersController extends FOSRestController
{

    /**
     * @View()
     */
    public function getUsersAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'username' );

        $em = $this->getDoctrine()->getManager();
        $userCollection = $em->getRepository( 'AppBundle:User' )->findBy(
                [], [$dstore['sort-field'] => $dstore['sort-direction']], $dstore['limit'], $dstore['offset'] );
        $data = [];
        foreach( $userCollection as $u )
        {
            $item = [
                'id' => $u->getId(),
                'username' => $u->getUsername(),
                'email' => $u->getEmail(),
                'enabled' => $u->isEnabled(),
                'locked' => $u->isLocked()
            ];
            $data[] = $item;
        }
        return $data;
    }

    /**
     * @View()
     */
    public function getUserAction( $username )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $user = $this->get( 'fos_user.user_manager' )->findUserBy( ['username' => $username] );
        if( $user !== null )
        {
            $data = [
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'groups' => $user->getGroups(),
                'enabled' => $user->isEnabled(),
                'locked' => $user->isLocked()
            ];
            return $data;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     */
    public function postUserAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $formProcessor->validateFormData( $this->createForm( UserType::class, null, [] ), $data );
        $manipulator = $this->get( 'fos_user.util.user_manipulator' );
        $user = $manipulator->create( $data['username'], md5( rand( 5, 10 ) ), $data['email'], false, false );
        if( $user !== null )
        {
            $response = new Response();
            $response->setStatusCode( 201 );
            $response->headers->set( 'Location', $this->generateUrl(
                            'app_admin_user_get_user', array('username' => $user->getUsername()), true // absolute
                    )
            );
            return $response;
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function putUserAction( $username, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $formProcessor->validateFormData( $this->createForm( UserType::class, null, [] ), $data );
        $userManager = $this->get( 'fos_user.user_manager' );
        $user = $userManager->findUserBy( ['username' => $username] );
        $user->setEmail( $data['email'] );
        $user->setEnabled( $data['enabled'] );
        $user->setLocked( $data['locked'] );
        $userManager->updateUser( $user, true );
    }

    /**
     * @View()
     */
    public function deleteUserAction( $username )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $user = $this->get( 'fos_user.user_manager' )->findUserById( $id );
        if( $user !== null )
        {
            // Do delete
            $status = JsonResponse::HTTP_OK;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

}
