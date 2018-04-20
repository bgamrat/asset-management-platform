<?php

Namespace App\Controller\Api\Common\Categories;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/categories")
     */
    public function getCategoriesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $name = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['c.id', "c.fullName AS name"] )
                    ->from( 'App\Entity\Asset\Category', 'c' )
                    ->where("c.name != 'top'")->andWhere( "LOWER(c.fullName) LIKE :category_name" )
                    ->setParameter( 'category_name', strtolower( $name ) );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }
}
