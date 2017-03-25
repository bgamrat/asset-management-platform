<?php

namespace AppBundle\Controller\Api\Common\Barcodes;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends FOSRestController
{

    /**
     * @Route("/api/store/barcodes")
     */
    public function getBarcodesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_USER', null, 'Unable to access this page!' );

        $barcode = $request->get( 'name' );
        if( !empty( $barcode ) )
        {
            $barcode = sprintf('%%%s%%', trim(urldecode($barcode),'*' ));
            $em = $this->getDoctrine()->getManager();
            $queryBuilder = $em->createQueryBuilder()->select( ['a.id', "CONCAT(CONCAT(b.barcode,' - '),CONCAT(CONCAT(bd.name,' '),m.name)) AS name" ])
                    ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                    ->join( 'a.model', 'm')
                    ->join('m.brand', 'bd')
                    ->join( 'a.barcodes', 'b' )
                    ->where( "LOWER(CONCAT(CONCAT(b.barcode,' - '),CONCAT(CONCAT(bd.name,' '),m.name))) LIKE :barcode" )
                    ->setParameter( 'barcode', strtolower( $barcode ) );
            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }
}
