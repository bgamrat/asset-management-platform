<?php

namespace AppBundle\Controller\Api\Admin;

use AppBundle\Entity\Person;
use AppBundle\Entity\User;
use AppBundle\Util\DStore;
use AppBundle\Util\Group;
use AppBundle\Form\Admin\User\UserType;
use AppBundle\Form\Admin\User\InvitationType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UsersController extends FOSRestController
{

    /**
     * @View()
     */
    public function getUsersAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_USER', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'username' );

        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $queryBuilder = $em->createQueryBuilder()->select( ['u'] )
                ->from( 'AppBundle:User', 'u' )
                ->orderBy( 'u.' . $dstore['sort-field'], $dstore['sort-direction'] );
        if( $dstore['limit'] !== null )
        {
            $queryBuilder->setMaxResults( $dstore['limit'] );
        }
        if( $dstore['offset'] !== null )
        {
            $queryBuilder->setFirstResult( $dstore['offset'] );
        }
        $filterQuery = null;
        if( $dstore['filter'] !== null )
        {
            switch( $dstore['filter'][DStore::OP] )
            {
                case DStore::LIKE:
                    $filterQuery = $queryBuilder->expr()->orX(
                            $queryBuilder->expr()->like( 'u.username', '?1' ), $queryBuilder->expr()->like( 'u.email', '?1' )
                    );
                    break;
                case DStore::GT:
                    $filterQuery = $queryBuilder->expr()->gt( 'u.username', '?1' );
            }
            $queryBuilder->setParameter( 1, $dstore['filter'][DStore::VALUE] );
        }
        if( !$this->isGranted( 'ROLE_ADMIN_USER_ADMIN' ) )
        {
            $deletedQuery = $queryBuilder->expr()->isNull( 'u.deletedAt' );
            if( $filterQuery === null )
            {
                $filterQuery = $deletedQuery;
            }
            else
            {
                $filterQuery = $queryBuilder->expr()->andX( $filterQuery, $deletedQuery );
            }
        }
        if( $filterQuery !== null )
        {
            $queryBuilder->where( $filterQuery );
        }
        $query = $queryBuilder->getQuery();
        $userCollection = $query->getResult();
        $data = [];
        foreach( $userCollection as $u )
        {
            $item = [
                'username' => $u->getUsername(),
                'email' => $u->getEmail(),
            ];
            if( $this->isGranted( 'ROLE_ADMIN_USER' ) )
            {
                $item['enabled'] = $u->isEnabled();
                $item['locked'] = $u->isLocked();
            }
            if( $this->isGranted( 'ROLE_ADMIN_USER_ADMIN' ) )
            {
                $item['deleted_at'] = $u->getDeletedAt();
            }
            $data[] = $item;
        }
        return $data;
    }

    /**
     * @View()
     */
    public function getUserAction( $username )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_USER', null, 'Unable to access this page!' );

        $user = $this->get( 'fos_user.user_manager' )->findUserBy( ['username' => $username] );
        if( $user !== null )
        {
            $data = [
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ];
            $person = $user->getPerson();
            $personModel = $this->get( 'app.model.person' );
            $data['person'] = $personModel->get( $person );

            if( $this->isGranted( 'ROLE_ADMIN_USER' ) )
            {
                $data['enabled'] = $user->isEnabled();
                $data['locked'] = $user->isLocked();
                $data['roles'] = $user->getRoles();
                $data['groups'] = [];
                foreach( $user->getGroups() as $group )
                {
                    $data['groups'][] = $group->getId();
                }
            }
            return $data;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     */
    public function postUserAction( $username, Request $request )
    {
        return $this->putUserAction( $username, $request );
    }

    /**
     * @View()
     */
    public function putUserAction( $username, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_USER_ADMIN', null, 'Unable to access this page!' );
        $response = new Response();
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $form = $this->createForm( UserType::class, null, [] );
        try
        {
            $formProcessor->validateFormData( $form, $data );
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository( 'AppBundle:User' )->findOneBy( ['username' => $username] );
            if( $user === null )
            {
                $user = $userManager->createUser();
                $user->setUsername( $data['username'] );
                $user->setPassword( md5( 'junk' ) );
            }
            $userUtil = $this->get( 'app.util.user' );
            $userUtil->processRoleUpdates( $user, $form->get( 'roles' )->getData() );
            $userUtil->processGroupUpdates( $user, $data );
            $personModel = $this->get( 'app.model.person' );
            $person = $personModel->update( $user->getPerson(), $data['person'] );
            $user->setPerson( $person );
            $em->persist( $user );
            $em->flush();

            $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
            $response->headers->set( 'Location', $this->generateUrl(
                            'app_admin_api_user_get_user', array('username' => $user->getUsernameCanonical()), true // absolute
                    )
            );
        }
        catch( Exception $e )
        {
            $response->setStatusCode( 400 );
            $response->setContent( json_encode(
                            ['message' => 'errors', 'errors' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTraceAsString()]
            ) );
        }
        return $response;
    }

    /**
     * @View(statusCode=204)
     */
    public function patchUserAction( $username, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_USER_ADMIN', null, 'Unable to access this page!' );
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $userManager = $this->get( 'fos_user.user_manager' );
        $user = $userManager->findUserBy( ['username' => $username] );
        if( $user !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'enabled':
                        $user->setEnabled( $value );
                        break;
                    case 'locked':
                        $user->setLocked( $value );
                        break;
                }
                $userManager->updateUser( $user, true );
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteUserAction( $username )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->enable( 'softdeleteable' );
        $user = $em->getRepository( 'AppBundle:User' )->findOneBy( ['username' => $username] );
        if( $user !== null )
        {
            $em->remove( $user );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

}
