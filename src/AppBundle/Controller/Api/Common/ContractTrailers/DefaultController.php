<?php

namespace AppBundle\Controller\Api\Common\ContractTrailers;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends FOSRestController
{

    /**
     * @Route("/api/store/contracttrailers")
     */
    public function getContracttrailersAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        if( !empty( $id ) )
        {
            $contractRepository = $this->getDoctrine()
                    ->getRepository( 'AppBundle\Entity\Client\Contract' );

            $contract = $contractRepository->find( $id );
            $data = ['id' => $id,
                'required' => $contract->getRequiresTrailers( false ),
                'available' => $contract->getAvailableTrailers( false )];
        }
        else
        {
            $data = null;
        }
        return $data;
    }
}