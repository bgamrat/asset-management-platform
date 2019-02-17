<?php

Namespace App\Controller\Api\Common\Trailers;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/trailers")
     */
    public function getTrailersAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_USER', null, 'Unable to access this page!' );
        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $name = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['t.id', 't.name'] )
                    ->from( 'App\Entity\Asset\Trailer', 't' );
            $queryBuilder
                    ->where( $queryBuilder->expr()->like( 'LOWER(t.name)', ':name' ) )
                    ->setParameter( 'name', strtolower( $name ) );

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