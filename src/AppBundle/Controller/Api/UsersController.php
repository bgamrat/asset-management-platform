<?php

namespace AppBundle\Controller\Api;

use AppBundle\Form\Admin\User\UserType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;

class UsersController extends FOSRestController
{

    /**
     * @View()
     */
    public function getUsersAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        if( $request->headers->has( 'X-Range' ) )
        {
            // TODO: Add validation
            $range = $request->headers->get( 'X-Range' );
            $values = explode( '-', explode( '=', $range )[1] );
            $offset = $values[0];
            $limit = $values[1] - $offset;
        }

        $em = $this->getDoctrine()->getManager();
        $userCollection = $em->getRepository( 'AppBundle:User' )->findBy( [], ['username' => 'asc'], $limit, $offset );
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
    public function getUserAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $user = $this->get( 'fos_user.user_manager' )->findUserBy( ['id' => $id] );
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
     * @View()
     */
    public function postUserAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $user = $this->get( 'fos_user.user_manager' )->findUserBy( [ 'id' => $id] );
        if( $user !== null )
        {
            $user_form = $this->createForm( UserType::class, $user );
            $status = JsonResponse::HTTP_CONFLICT;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function putUserAction( $id, Request $request )
    {
        // TODO: Add validation
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $data = json_decode( $request->getContent(), true );
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository( 'AppBundle:User' )->find( $id );
        $user->setEmail( $data['email'] );
        $user->setEnabled( $data['enabled'] );
        $user->setLocked( $data['locked'] );
        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        $em->flush();
    }

    /**
     * @View()
     */
    public function deleteUserAction( $id )
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
