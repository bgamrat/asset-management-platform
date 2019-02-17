<?php

Namespace App\Controller\Admin\Schedule;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of ParkController
 *
 * @author bgamrat
 */
class ParkController extends AbstractController
{
    /**
     * @Route("/admin/schedule/park", methods={"GET"})
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
      
        return $this->render( 'admin/schedule/park.html.twig' );
    }
}