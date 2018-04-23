<?php

Namespace App\Controller\Api\Admin\User;

use App\Entity\Common\Person;
use App\Entity\Group;
use App\Util\DStore;
use App\Util\Form as FormUtil;
use App\Form\Admin\User\GroupType;
use App\Form\Admin\Group\InvitationType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GroupsController extends FOSRestController
{

    private $dstore;
    private $formUtil;

    public function __construct( DStore $dstore, FormUtil $formUtil ) {
        $this->dstore = $dstore;
        $this->formUtil = $formUtil;
    }

    /**
     * @View()
     */
    public function getGroupsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_GROUP', null, 'Unable to access this page!' );
        $dstore = $this->dstore->gridParams( $request, 'name' );

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder()->select( ['g'] )
                ->from( 'App\Entity\Group', 'g' )
                ->orderBy( 'g.' . $dstore['sort-field'], $dstore['sort-direction'] );
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
                    $filterQuery = $queryBuilder->expr()->like( 'g.name', '?1' );
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
        $data = $queryBuilder->getQuery()->getResult();
        return array_values( $data );
    }

    /**
     * @View()
     */
    public function getGroupAction( $name )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_GROUP', null, 'Unable to access this page!' );

        $group = $this->get( 'fos_user.group_manager' )->findGroupByName( $name );

        if( $group !== null )
        {
            $form = $this->createForm( GroupType::class, $group, ['allow_extra_fields' => true] );
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
    public function postGroupAction( Request $request )
    {
        return $this->putGroupAction( $request );
    }

    /**
     * @View()
     */
    public function putGroupAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_GROUP_ADMIN', null, 'Unable to access this page!' );
        $response = new Response();
        $em = $this->getDoctrine()->getManager();
        $groupManager = $this->get( 'fos_user.group_manager' );
        $data = $request->request->all();
        if( $data['id'] === null )
        {
            $group = $groupManager->createGroup( $data['name'] );
        }
        else
        {
            $group = $em->getRepository( 'App\Entity\Group' )->find( $data['id'] );
        }
        $form = $this->createForm( GroupType::class, $group, [] );
        try
        {
            $form->submit( $data );
            if( $form->isValid() )
            {
                $group = $form->getData();
                $groupManager->updateGroup( $group );
                $em->persist( $group );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_user_groups_get_group', array('id' => $group->getId()), true // absolute
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
        $group = $em->getRepository( 'App\Entity\Group' )->findOneBy( ['groupname' => $groupname] );
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
