<?php

Namespace App\Controller\Admin\Schedule;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of ShopController
 *
 * @author bgamrat
 */
class ShopController extends AbstractController
{
    /**
     * @Route("/admin/schedule/shop", methods={"GET"})
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
      
        return $this->render( 'admin/schedule/shop.html.twig' );
    }
}