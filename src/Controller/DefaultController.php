<?php

Namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Entity\Asset\AssetStatus;
use Form\Admin\Common\TestType;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="root")
     */
    public function indexAction( Request $request )
    {
        return $this->render( 'public/index.html.twig', ['no_settings' => true] );
    }

    /**
     * @Route("/test")
     */
    public function testAction( Request $request )
    {
        /*
          $assetStatus = new AssetStatus();
          $assetStatus->setName('~!');
          $validator = $this->get('validator');
          $constraint = $validator->getMetadataFor($assetStatus);
         */
        $testForm = $this->createForm( TestType::class, null );

        return $this->render( 'public/test.html.twig', ['test_form' => $testForm->createView(), 'no_hide' => true ] );
    }

}
