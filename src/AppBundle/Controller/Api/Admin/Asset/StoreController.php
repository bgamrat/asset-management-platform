<?php

namespace AppBundle\Controller\Api\Admin\Asset;

use AppBundle\Util\DStore;
use AppBundle\Entity\Model;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class StoreController extends FOSRestController
{

    /**
     * @View()
     */
    public function getModelsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $brandModel = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['m.id', "CONCAT(CONCAT(b.name, ' '), m.name) AS name"] )
                    ->from( 'AppBundle:Model', 'm' )
                    ->innerJoin( 'm.brand', 'b' )
                    ->where( "CONCAT(CONCAT(b.name, ' '), m.name) LIKE :brand_model" )
                    ->setParameter( 'brand_model', $brandModel );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

    /**
     * @View()
     */
    public function getModelAction( $brandModel )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder()->select( ['m.id', "CONCAT(CONCAT(b.name, ' '), m.name) AS name"] )
                ->from( 'AppBundle:Model', 'm' )
                ->innerJoin( 'm.brand', 'b' )
                ->where( "CONCAT(CONCAT(b.name, ' '), m.name) LIKE :brand_model" )
                ->setParameter( 'brand_model', $brandModel );

        $data = $queryBuilder->getQuery()->getResult();
        return $data;
    }

}
