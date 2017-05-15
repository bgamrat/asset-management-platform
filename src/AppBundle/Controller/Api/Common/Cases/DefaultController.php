<?php

namespace AppBundle\Controller\Api\Common\Cases;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/cases")
     */
    public function getCasesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $barcode_model = $request->get( 'name' );
        if( !empty( $barcode_model ) )
        {
            $barcode = '%' . str_replace( '*', '%', $barcode_model );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['a.id', "CONCAT(b.barcode,' ',m.name) AS name"] )
                    ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                    ->innerJoin( 'a.barcodes', 'b' )
                    ->innerJoin( 'a.model', 'm' );
            $queryBuilder
                    ->where( "LOWER(CONCAT(CONCAT(b.barcode, ' '), m.name)) LIKE :barcode_model" )
                    ->andWhere( 'm.container = true' )
                    ->setParameter( 'barcode_model', strtolower( $barcode_model ) );

            $data = $queryBuilder->getQuery()->getResult();
            foreach ($data as $i => $d) {
                $data[$i]['label'] = '<span>'.$d['name'].'</span>';
            }
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
