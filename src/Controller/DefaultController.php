<?php

Namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Asset\AssetStatus;
use App\Form\Admin\Common\TestType;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="root")
     */
    public function indexAction( Request $request )
    {
        return $this->render( 'default/public/index.html.twig', ['no_settings' => true] );
    }

    /**
     * @Route("/test")
     */
    public function testAction( Request $request )
    {
        return $this->render( 'public/index.html.twig' );
    }

}
