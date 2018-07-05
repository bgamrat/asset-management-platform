<?php

Namespace App\Controller\Api\Admin\User;

use App\Entity\Common\Person;
use App\Entity\User;
use App\Util\DStore;
use App\Util\Log;
use App\Util\Form as FormUtil;
use App\Form\Admin\User\UserType;
use App\Form\Admin\User\InvitationType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\View\View as FOSRestView;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UsersController extends FOSRestController
{

    private $dstore;
    private $log;
    private $formUtil;

    public function __construct( DStore $dstore, Log $log, FormUtil $formUtil ) {
        $this->dstore = $dstore;
        $this->log = $log;
        $this->formUtil = $formUtil;
    }
    /**
     * @View()
     */
    public function getUsersAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_USER', null, 'Unable to access this page!' );
        $dstore = $this->dstore->gridParams( $request, 'username' );

        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $queryBuilder = $em->createQueryBuilder()->select( ['u'] )
                ->from( 'App\Entity\User', 'u' )
                ->orderBy( 'u.' . $dstore['sort-field'], $dstore['sort-direction'] );
        $limit = 0;
        if( $dstore['limit'] !== null )
        {
            $limit = $dstore['limit'];
            $queryBuilder->setMaxResults( $limit );
        }
        $offset = 0;
        if( $dstore['offset'] !== null )
        {
            $offset = $dstore['offset'];
            $queryBuilder->setFirstResult( $offset );
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
        $count = $em->getRepository( 'App\Entity\User' )->count([]);
        $view = FOSRestView::create();
        $view->setData( $data );
        $view->setHeader( 'Content-Range', 'items ' . $offset . '-' . ($offset + $limit) . '/' . $count );
        $handler = $this->get( 'fos_rest.view_handler' );
        return $handler->handle( $view );
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
        $user = $em->getRepository( 'App\Entity\User' )->find( $id );
        if( $user !== null )
        {
            $logUtil = $this->log;
            $logUtil->getLog( 'App\Entity\UserLog', $id );
            $history = $logUtil->translateIdsToText();
            $formUtil = $this->formUtil;
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
            $user = $em->getRepository( 'App\Entity\User' )->find( $id );
            $person = $user->getPerson( true );
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
        $user = $em->getRepository( 'App\Entity\User' )->findOneBy( ['username' => $username] );
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
