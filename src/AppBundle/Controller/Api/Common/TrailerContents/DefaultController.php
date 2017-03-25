<?php

namespace AppBundle\Controller\Api\Common\TrailerContents;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends FOSRestController
{

    /**
     * @Route("/api/store/trailercontents")
     */
    public function getTrailercontentsAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_USER', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder()->select( 'a.id' )
                ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                ->innerJoin( 'a.model', 'm' )
                ->leftJoin( 'a.location', 'l' )
                ->leftJoin( 'l.type', 'lt' )
                ->where( "l.entity = ?1 AND lt.entity = 'trailer' AND m.container = true" );
        $queryBuilder->setParameter( 1, $id );
        $data = $queryBuilder->getQuery()->getResult();
        $containers = [];
        foreach( $data as $c )
        {
            $containers[] = $c['id'];
        }

        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'id' );
        switch( $dstore['sort-field'] )
        {
            case 'barcode':
                $sortField = 'bc.barcode';
                break;
            case 'model':
            case 'model_text':
                $sortField = 'm.name';
                break;
            case 'category':
                $sortField = 'c.name';
                break;
            default:
                $sortField = 'a.' . $dstore['sort-field'];
        }

        $columns = ['a.id', 'bc.barcode',
            "CONCAT(CONCAT(b.name,' '),m.name) AS model_text", 'm.id AS model', 'a.serial_number', 'a.location_text',
            's.name AS status_text', 'c.name AS category_text',
            'a.comment', 'a.active'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 'a.deletedAt AS deleted_at';
        }

        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                ->innerJoin( 'a.model', 'm' )
                ->innerJoin( 'm.category', 'c' )
                ->innerJoin( 'm.brand', 'b' )
                ->leftJoin( 'a.location', 'l' )
                ->leftJoin( 'l.type', 'lt' )
                ->leftJoin( 'a.barcodes', 'bc', 'WITH', 'bc.active = true' )
                ->leftJoin( 'a.status', 's' )
                ->where( "l.entity = ?1 AND lt.entity = 'trailer'" )
                ->orWhere( "l.entity IN (?2) AND lt.entity='asset'" )
                ->orderBy( $sortField, $dstore['sort-direction'] );
        $queryBuilder->setParameter( 1, $id );
        $queryBuilder->setParameter( 2, $containers );
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
                    $queryBuilder->andWhere(
                            "LOWER(CONCAT(CONCAT(b.name,' '),m.name)) LIKE ?3 OR 
                            LOWER(c.name) LIKE ?3 OR LOWER(a.serial_number) LIKE ?3" );
                    break;
                case DStore::GT:
                    $queryBuilder->andWhere(
                            $queryBuilder->expr()->gt( "LOWER(CONCAT(CONCAT(b.name,' '),m.name))", '?3' )
                    );
            }
            $queryBuilder->setParameter( 3, strtolower( $dstore['filter'][DStore::VALUE] ) );
        }
        $data = $queryBuilder->getQuery()->getResult();
        return $data;
    }

}
