<?php

namespace AppBundle\Controller\Api\Common\Contracts;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

class DefaultController extends FOSRestController
{

    /**
     * @View()
     * @Route("/api/store/contracts")
     */
    public function getContractsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get( 'name' );
        if( !empty( $name ) )
        {
            $em = $this->getDoctrine()->getManager();
            $data = $em->getRepository( 'AppBundle\Entity\Client\Contract' )->findByNameLike( $name );
            foreach ($data as $c) {
                $c->setName($c->getClient()->getName().' '.$c->getName());
            }
        }
        else
        {
            $data = null;
        }
        return $data;
    }
}
