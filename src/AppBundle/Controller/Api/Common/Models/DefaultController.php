<?php

namespace AppBundle\Controller\Api\Common\Models;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/models")
     */
    public function getModelsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        $ca = $request->get( 'ca' );
        if( !empty( $name ) )
        {
            $brandModel = '%' . str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();
            $select = ['m.id', "CONCAT(CONCAT(b.name, ' '), m.name) AS name"];
            if( $ca !== null )
            {
                $select[] = 'm.custom_attributes';
            }
            $queryBuilder = $em->createQueryBuilder()->select( $select )
                    ->from( 'AppBundle\Entity\Asset\Model', 'm' )
                    ->innerJoin( 'm.brand', 'b' )
                    ->where( "LOWER(CONCAT(CONCAT(b.name, ' '), m.name)) LIKE :brand_model" )
                    ->orderBy( 'name' )
                    ->setParameter( 'brand_model', strtolower( $brandModel ) );


            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
