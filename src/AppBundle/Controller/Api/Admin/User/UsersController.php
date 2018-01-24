<?php

namespace AppBundle\Controller\Api\Admin\User;

use AppBundle\Entity\Common\Person;
use AppBundle\Entity\User;
use AppBundle\Util\DStore;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use AppBundle\Form\Admin\User\UserType;
use AppBundle\Form\Admin\User\InvitationType;
use FOS\RestBundle\Controller\FOSRestController;
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
                'id' => $u->getId(),
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
    public function getUserAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_USER', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $user = $em->getRepository( 'AppBundle\Entity\User' )->find( $id );
        if( $user !== null )
        {
            $logUtil = $this->get( 'app.util.log' );
            $logUtil->getLog( 'AppBundle\Entity\UserLog', $id );
            $history = $logUtil->translateIdsToText();
            $formUtil = $this->get( 'app.util.form' );
            $formUtil->saveDataTimestamp( 'user' . $user->getId(), $user->getUpdatedAt() );

            $form = $this->createForm( UserType::class, $user, ['allow_extra_fields' => true] );
            $user->setHistory( $history );
            $form->add( 'history', TextareaType::class, ['data' => $history] );
            return $form->getViewData();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @View()
     */
    public function postUserAction( $id, Request $request )
    {
        return $this->putUserAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putUserAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_USER_ADMIN', null, 'Unable to access this page!' );
        $response = new Response();
        $em = $this->getDoctrine()->getManager();

        $data = $request->request->all();
        $username = $data['username'];
        if( $id === "null" )
        {
            $userManager = $this->get( 'fos_user.user_manager' );
            $user = $userManager->createUser();
            $user->setUsername( $data['username'] );
            $user->setPassword( md5( 'junk' ) );
        }
        else
        {
            $user = $em->getRepository( 'AppBundle\Entity\User' )->find( $id );
            $person = $user->getPerson(true);
        }
        $form = $this->createForm( UserType::class, $user, [] );
        try
        {
            $form->submit( $data );
            if( $form->isValid() )
            {
                // Clear out prior assignments to avoid constraints
                if( !empty( $person ) )
                {
                    $person->setUser( null );
                    $em->persist( $person );
                }
                if( !empty( $user ) )
                {
                    $user->setPerson( null );
                    $em->persist( $user );
                }
                $em->flush();
                $user = $form->getData();
                $em->persist( $user );
                $updatedPerson = $form->get( 'person' )->getData();
                $updatedPerson->setUser( $user );
                $user->setPerson( $updatedPerson );
                $em->persist( $updatedPerson );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_user_get_user', array('id' => $user->getId()), true // absolute
                        )
                );
            }
            else
            {
                return $form;
            }
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
    public function patchUserAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_USER_ADMIN', null, 'Unable to access this page!' );
        $data = $request->request->all();
        $userManager = $this->get( 'fos_user.user_manager' );
        $user = $userManager->findUserBy( ['id' => $id] );
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
