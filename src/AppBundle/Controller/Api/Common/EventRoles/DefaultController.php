<?php

namespace AppBundle\Controller\Api\Common\EventRoles;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/eventroles")
     */
    public function getEventRolesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $name = str_replace( '*', '%', $name );

            $em = $this->getDoctrine()->getManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['er.id', "er.name"] )
                    ->from( 'AppBundle\Entity\Schedule\EventRoleType', 'er' )
                    ->where( 'LOWER(er.name) LIKE :event_role_name' )
                    ->orderBy( 'er.name')
                    ->setParameter( 'event_role_name', strtolower( $name ) );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
