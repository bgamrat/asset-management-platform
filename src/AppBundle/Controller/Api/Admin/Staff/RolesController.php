<?php

namespace AppBundle\Controller\Api\Admin\Staff;

use AppBundle\Util\DStore;
use AppBundle\Entity\Staff\Role;
use AppBundle\Form\Admin\Staff\RoleType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RolesController extends FOSRestController
{

    /**
     * @View()
     */
    public function getRolesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'id' );
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
        $columns = ['r.id'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 'r.deletedAt AS deleted_at';
        }
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle\Entity\Staff\Role', 'r' )
                ->orderBy( $sortField, $dstore['sort-direction'] );
        if( $dstore['limit'] !== null )
        {
            $queryBuilder->setMaxResults( $dstore['limit'] );
        }
        if( $dstore['offset'] !== null )
        {
            $queryBuilder->setFirstResult( $dstore['offset'] );
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

        $ids = $queryBuilder->getQuery()->getResult();

        $data = [];
        foreach( $ids as $i => $row )
        {
            $role = $em->getRepository( 'AppBundle\Entity\Common\Role' )->find( $row['id'] );
            $p = ['id' => $row['id'],
                'name' => $role->getName(),
                'default' => $role->getDefault(),
                'comment' => $role->getComment(),
                'active' => $role->isActive()];
            $data[] = $p;
        }
        return array_values( $data );
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
                        ->getRepository( 'AppBundle\Entity\Common\Role' )->find( $id );
        if( $role !== null )
        {
            $formUtil = $this->get( 'app.util.form' );
            $formUtil->saveDataTimestamp( 'role' . $role->getId(), $role->getUpdatedAt() );

            $form = $this->createForm( RoleType::class, $role, ['allow_extra_fields' => true] );

            $logUtil = $this->get( 'app.util.log' );
            $logUtil->getLog( 'AppBundle\Entity\Common\RoleLog', $id );
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
            $role = $em->getRepository( 'AppBundle\Entity\Common\Role' )->find( $id );
            $formUtil = $this->get( 'app.util.form' );
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
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'AppBundle\Entity\Common\Role' );
        $role = $repository->find( $id );
        if( $role !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
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
        $role = $em->getRepository( 'AppBundle\Entity\Common\Role' )->find( $id );
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
