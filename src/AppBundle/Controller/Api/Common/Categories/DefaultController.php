<?php

namespace AppBundle\Controller\Api\Common\Categories;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends FOSRestController
{

    /**
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
                    ->from( 'AppBundle\Entity\Asset\Category', 'c' )
                    ->where( "LOWER(c.name) LIKE :category_name" )
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
