<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Model\GroupManager as GroupManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Group;
use AppBundle\Form\Admin\User\UserType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;

class UserController extends FOSRestController
{

    /**
     * @Route("/admin/user")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $groups = $this->get( 'fos_user.group_manager' )->findGroups();
        $groupNames = [ ];
        foreach ( $groups as $g )
        {
            $groupNames[$g->getName()] = $g->getId();
        }

        $user = new User();
        $user_form = $this->createForm( UserType::class, $user, ['groups' => $groupNames ] );

        return $this->render( 'admin/user/index.html.twig', array(
                    'user_form' => $user_form->createView(),
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }

    /**
     * @Route("/api/admin/user/list")
     * @Method({"GET"})
     * @View(statusCode=200)
     */
    public function apiUserListAction()
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $users = $this->get( 'fos_user.user_manager' )->findUsers();

        $data = [ ];
        foreach ( $users as $u )
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
     *  @Route("/api/admin/user/{id}.json")
     *  @Method("GET")
     *  @View()
     */
    public function apiUserGetAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $user = $this->get( 'fos_user.user_manager' )->findUserBy( ['id' => $id ] );
        if ( $user !== null )
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
     *  @Route("/api/admin/user/{id}.json")
     *  @Method({"POST"})
     *  @ParamConverter("post", converter="fos_rest.request_body")
     *  @View(statusCode=201)
     */
    public function apiUserPostAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $user = $this->get( 'fos_user.user_manager' )->findUserBy( [ 'id' => $id ] );
        if ( $user !== null )
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
     *  @Route("/api/admin/user/{id}.json")
     *  @Method({"PUT"})
     *  @View(statusCode=204)
     *  @ParamConverter("put", converter="fos_rest.request_body")
     */
    public function apiUserPutAction( Request $request, $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $data = null;
        $user = $this->get( 'fos_user.user_manager' )->findUserBy( [ 'id' => $id ] );
        if ( $user !== null )
        {
            $user = new User();
            $user_form = $this->createForm( UserType::class, $user );
            $user_form->handleRequest( $request );
            if ( $user_form->isValid() )
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist( $user );
                $em->flush();
              
            }
            return $user_form;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        };
    }

    /**
     *  @Route("/api/admin/user/{id}.json")
     *  @Method({"DELETE"})
     *  @View(statusCode=200)
     */
    public function apiUserDeleteAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $user = $this->get( 'fos_user.user_manager' )->findUserById( $id );
        if ( $user !== null )
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
