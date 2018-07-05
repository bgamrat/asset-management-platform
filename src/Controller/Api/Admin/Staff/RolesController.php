<?php

Namespace App\Controller\Api\Admin\Staff;

use App\Util\DStore;
use App\Util\Log;
use App\Entity\Staff\Role;
use App\Form\Admin\Staff\RoleType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\View\View as FOSRestView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RolesController extends FOSRestController
{

    private $dstore;
    private $log;

    public function __construct( DStore $dstore, Log $log ) {
        $this->dstore = $dstore;
        $this->log = $log;
    }

    /**
     * @View()
     */
    public function getRolesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->dstore->gridParams( $request, 'id' );
        switch( $dstore['sort-field'] )
        {
            case 'name':
                $sortField = 'r.name';
                break;
            default:
                $sortField = 'r.' . $dstore['sort-field'];
        }
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $columns = ['r.id', 'r.name', 'r.comment', 'r.active', 'r.default'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 'r.deletedAt AS deleted_at';
        }
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'App\Entity\Staff\Role', 'r' )
                ->orderBy( $sortField, $dstore['sort-direction'] );
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
        if( $dstore['filter'] !== null )
        {
            switch( $dstore['filter'][DStore::OP] )
            {
                case DStore::LIKE:
                    $queryBuilder->where(
                            $queryBuilder->expr()->like( 'LOWER(r.name)', '?1' )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( 'LOWER(r.name)', '?1' )
                    );
            }
            $queryBuilder->setParameter( 1, strtolower( $dstore['filter'][DStore::VALUE] ) );
        }
        $data = $queryBuilder->getQuery()->getResult();
        $count = $em->getRepository( 'App\Entity\Staff\Role' )->count([]);
        $view = FOSRestView::create();
        $view->setData( $data );
        $view->setHeader( 'Content-Range', 'items ' . $offset . '-' . ($offset + $limit) . '/' . $count );
        $handler = $this->get( 'fos_rest.view_handler' );
        return $handler->handle( $view );
    }

    /**
     * @View()
     */
    public function getRoleAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $role = $this->getDoctrine()
                        ->getRepository( 'App\Entity\Common\Role' )->find( $id );
        if( $role !== null )
        {
            $formUtil = $this->formUtil;
            $formUtil->saveDataTimestamp( 'role' . $role->getId(), $role->getUpdatedAt() );

            $form = $this->createForm( RoleType::class, $role, ['allow_extra_fields' => true] );

            $logUtil = $this->log;
            $logUtil->getLog( 'App\Entity\Common\RoleLog', $id );
            $history = $logUtil->translateIdsToText();

            $role->setHistory( $history );
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
    public function postRoleAction( $id, Request $request )
    {
        return $this->putRoleAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putRoleAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $data = $request->request->all();
        if( $id === "null" )
        {
            $role = new Role();
        }
        else
        {
            $role = $em->getRepository( 'App\Entity\Common\Role' )->find( $id );
            $formUtil = $this->formUtil;
            if( $formUtil->checkDataTimestamp( 'role' . $role->getId(), $role->getUpdatedAt() ) === false )
            {
                throw new Exception( "data.outdated", 400 );
            }
        }
        $form = $this->createForm( RoleType::class, $role, ['allow_extra_fields' => true] );
        try
        {
            $form->submit( $data );
            if( $form->isValid() )
            {
                $role = $form->getData();
                $em->persist( $role );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_role_get_role', array('id' => $role->getId()), true // absolute
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
    public function patchRoleAction( $id, Request $request )
    {
        $formProcessor = $this->formUtil;
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'App\Entity\Staff\Role' );
        $role = $repository->find( $id );
        if( $role !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'default':
                        $prevDefault = $repository->findBy( ['default' => true] );
                        foreach( $prevDefault as $pd )
                        {
                            $pd->setDefault( false );
                        }
                        $role->setDefault( true );
                        break;
                    case 'active':
                        $role->setActive( $value );
                        break;
                }

                $em->persist( $role );
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteRoleAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $role = $em->getRepository( 'App\Entity\Common\Role' )->find( $id );
        if( $role !== null )
        {
            $em->getFilters()->enable( 'softdeleteable' );
            $em->remove( $role );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

}
