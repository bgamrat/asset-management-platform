<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\Criteria;
use FOS\UserBundle\Model\GroupManager as GroupManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Group;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;

class UsersController extends FOSRestController
{

    /**
     * @Route("/admin/user")
     * @View()
     */
    public function cgetAction( Request $request )
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
     * @Route("/admin/user/{id}")
     * @View()
     */
    public function getAction( $id )
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

    public function postAction( Request $request )
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
     * @ParamConverter("userPut",  converter="fos_rest.request_body")
     * @View(statusCode=204)
     */
    public function putAction( $id, User $userPut, ConstraintViolationListInterface $validationErrors )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        if( count( $validationErrors ) > 0 )
        {
            die( 'not valid' );
        }
        else
        {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository( 'AppBundle:User' )->find( $id );
            $user->setEmail( $userPut->getEmailCanonical() );
            $user->setEnabled( $userPut->isEnabled() );
            $user->setLocked( $userPut->isLocked() );
            if( $userPut->getPassword() )
            {
                $user->setPassword( $userPut->getPassword() );
            }
            $em->flush();
        }
    }

    public function deleteAction( $id )
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
