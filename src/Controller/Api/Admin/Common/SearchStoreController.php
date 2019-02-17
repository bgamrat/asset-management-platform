<?php

Namespace App\Controller\Api\Admin\Common;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View;
use App\Entity\IdNameURL;

class SearchStoreController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/search")
     */
    public function getEntitiesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_USER', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) && $name !== '*' )
        {
            $data = [];
            $em = $this->getDoctrine()->getManager();
            $searchEntities = ['App\Entity\Asset\Asset' => 'app_admin_asset_equipment_index',
                'App\Entity\Common\Person' => 'app_admin_common_person_index',
                'App\Entity\Client\Client' => 'app_admin_client_client_index'];
            foreach( $searchEntities as $entity => $route )
            {
                $results = $em->getRepository( $entity )->findByNameLike( $name );
                if( !empty( $results ) )
                {
                    foreach( $results as $r )
                    {
                        $type = preg_replace( '#^.*\\\(\w+)$#', '$1', $entity );
                        $d = new IdNameURL( $r->getId(), $r->getName() . ' (' . $type . ')', $this->generateUrl(
                                        $route, array('id' => $r->getId()), true
                                ) );
                        $data[] = $d;
                    }
                }
            }
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
