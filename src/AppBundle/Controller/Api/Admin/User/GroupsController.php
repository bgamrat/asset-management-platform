<?php

namespace AppBundle\Controller\Api\Admin\User;

use AppBundle\Entity\Common\Person;
use AppBundle\Entity\Group;
use AppBundle\Util\DStore;
use AppBundle\Form\Admin\Group\GroupType;
use AppBundle\Form\Admin\Group\InvitationType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GroupsController extends FOSRestController
{

    /**
     * @View()
     */
    public function getGroupsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_GROUP', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'name' );

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder()->select( ['g'] )
                ->from( 'AppBundle:Group', 'g' )
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
                            $queryBuilder->expr()->like( 'g.name', '?1' ), $queryBuilder->expr()->like( 'u.email', '?1' )
                    );
                    break;
                case DStore::GT:
                    $filterQuery = $queryBuilder->expr()->gt( 'g.name', '?1' );
            }
            $queryBuilder->setParameter( 1, $dstore['filter'][DStore::VALUE] );
        }

        if( $filterQuery !== null )
        {
            $queryBuilder->where( $filterQuery );
        }
        $query = $queryBuilder->getQuery();
        $groupCollection = $query->getResult();
        $data = [];
        foreach( $groupCollection as $u )
        {
            $item = [
                'name' => $g->getName(),
                'comment' => $g->getComment(),
                'active' => $g->isActive()
            ];

            $data[] = $item;
        }
        return $data;
    }

    /**
     * @View()
     */
    public function getGroupAction( $groupname )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_GROUP', null, 'Unable to access this page!' );

        $group = $this->get( 'fos_group.group_manager' )->findGroupBy( ['groupname' => $groupname] );
        if( $group !== null )
        {
            $data = [
                'name' => $group->getName(),
                'comment' => $group->getComment(),
                'active' => $group->isActive()
            ];

            if( $this->isGranted( 'ROLE_ADMIN_GROUP_ADMIN' ) )
            {
                $data['roles'] = $group->getRoles();
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
    public function postGroupAction( $groupname, Request $request )
    {
        return $this->putGroupAction( $groupname, $request );
    }

    /**
     * @View()
     */
    public function putGroupAction( $groupname, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_GROUP_ADMIN', null, 'Unable to access this page!' );
        $response = new Response();
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        if( $id === "null" )
        {
                           $group = $groupManager->createGroup();
                $group->setGroupname( $data['groupname'] );
                $group->setPassword( md5( 'junk' ) );
        }
        else
        {
            $group = $em->getRepository( 'AppBundle\Entity\Group' )->findOneBy( ['groupname' => $groupname] );
        }
        $form = $this->createForm( GroupType::class, $group, [] );
        try
        {
            $form->submit( $data );
            if( $form->isValid() )
            {
                $group = $form->getData();
                $em->persist( $group );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_group_get_group', array('id' => $group->getId()), true // absolute
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
    public function patchGroupAction( $groupname, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_GROUP_ADMIN', null, 'Unable to access this page!' );
        $data = $request->request->all();
        $groupManager = $this->get( 'fos_group.group_manager' );
        $group = $groupManager->findGroupBy( ['groupname' => $groupname] );
        if( $group !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'enabled':
                        $group->setEnabled( $value );
                        break;
                    case 'locked':
                        $group->setLocked( $value );
                        break;
                }
                $groupManager->updateGroup( $group, true );
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteGroupAction( $groupname )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->enable( 'softdeleteable' );
        $group = $em->getRepository( 'AppBundle:Group' )->findOneBy( ['groupname' => $groupname] );
        if( $group !== null )
        {
            $em->remove( $group );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

}
