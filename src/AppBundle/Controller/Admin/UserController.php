<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Model\GroupManager as GroupManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Group;
use AppBundle\Form\Admin\User\UserType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;

class UserController extends FOSRestController
{

    /**
     * @Route("/admin/user")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $groups = $this->get( 'fos_user.group_manager' )->findGroups();
        $groupNames = [];
        foreach( $groups as $g )
        {
            $groupNames[$g->getName()] = $g->getId();
        }

        $user = new User();
        $user_form = $this->createForm( UserType::class, $user, ['groups' => $groupNames] );

        return $this->render( 'admin/user/index.html.twig', array(
                    'user_form' => $user_form->createView(),
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }

    /**
     * @Route("/api/admin/user/list")
     */
    public function apiUserListAction()
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $users = $this->get( 'fos_user.user_manager' )->findUsers();

        $data = [];
        foreach( $users as $u )
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

        // calls json_encode and sets the Content-Type header
        return new JsonResponse( $data );
    }

    /**
     *  @Route("/api/admin/user/{id}")
     *  @Method({"GET"})
     */
    public function apiUser( $id )
    {
        $serializer = $this->get( 'serializer' );
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
            $status = JsonResponse::HTTP_OK;
        }
        else
        {
            $status = JsonResponse::HTTP_NOT_FOUND;
        }

        return new JsonResponse( $data, $status );
    }

    /**
     *  @Route("/api/admin/user")
     *  @Method({"POST"})
     */
    public function apiUserPost( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $user = $this->get( 'fos_user.user_manager' )->findUserBy( [ 'id' => $id ] );
        if( $user !== null )
        {
            $user_form = $this->createForm( UserType::class, $user );
            $status = JsonResponse::HTTP_CONFLICT;
        }
        else
        {
            // Save data
            $status = JsonResponse::HTTP_CREATED;
        }

        return new JsonResponse( $data, $status, ['location' => '/api/admin/user/' . $username] );
    }

    /**
     *  @Route("/api/admin/user/{id}")
     *  @Method({"PUT"})
     */
    public function apiUserPut( Request $request, $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $data = null;
        $user = $this->get( 'fos_user.user_manager' )->findUserBy([ 'id' => $id ] );
        if( $user !== null )
        {
            $user = new User();
            $user_form = $this->createForm( UserType::class, $user );
            $user_form->handleRequest( $request );
            if( $user_form->isValid() )
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist( $user );
                $em->flush();
                $status = JsonResponse::HTTP_NO_CONTENT;
            }
            else
            {
                echo 'bad data';
            }

            $status = 201;
        }
        else
        {
            $status = JsonResponse::HTTP_NOT_FOUND;
        }

        return new JsonResponse( $data, $status );
    }

    /**
     *  @Route("/api/admin/user/{id}")
     *  @Method({"DELETE"})
     */
    public function apiUserDelete( $id )
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
            $status = JsonResponse::HTTP_NOT_FOUND;
        }

        return new JsonResponse( $data );
    }

}
