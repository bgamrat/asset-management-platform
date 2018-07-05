<?php

Namespace App\Controller\Api\Admin\User;

use App\Entity\Invitation;
use App\Util\DStore;
use App\Form\Admin\User\InvitationType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\View\View as FOSRestView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvitationsController extends FOSRestController
{

    private $dstore;

    public function __construct( DStore $dstore )
    {
        $this->dstore = $dstore;
    }

    /**
     * @View()
     */
    public function getInvitationsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->dstore->gridParams( $request, 'email' );

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder()->select( ['i'] )
                ->from( 'App\:Invitation', 'i' )
                ->orderBy( 'i.' . $dstore['sort-field'], $dstore['sort-direction'] );
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
                            $queryBuilder->expr()->orX(
                                    $queryBuilder->expr()->like( 'i.email', '?1' ), $queryBuilder->expr()->like( 'i.code', '?1' ) )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( 'i.email', '?1' )
                    );
            }
            $queryBuilder->setParameter( 1, $dstore['filter'][DStore::VALUE] );
        }
        $query = $queryBuilder->getQuery();
        $invitationCollection = $query->getResult();
        $data = [];
        foreach( $invitationCollection as $i )
        {
            $item = [
                'email' => $i->getEmail(),
                'sent' => $i->isSent()
            ];
            $data[] = $item;
        }
        $count = $em->getRepository( 'App\Entity\User\Invitation' )->count([]);
        $view = FOSRestView::create();
        $view->setData( $data );
        $view->setHeader( 'Content-Range', 'items ' . $offset . '-' . ($offset + $limit) . '/' . $count );
        $handler = $this->get( 'fos_rest.view_handler' );
        return $handler->handle( $view );
    }

}
