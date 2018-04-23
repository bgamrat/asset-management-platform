<?php

Namespace App\Controller\Api\Admin\User;

use App\Entity\Invitation;
use App\Util\DStore;
use App\Form\Admin\User\InvitationType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
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
        return $data;
    }

}
