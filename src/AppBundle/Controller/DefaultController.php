<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="root")
     */
    public function indexAction(Request $request)
    {
        return $this->render('public/index.html.twig');
    }
   
    /**
     * @Route("/test")
     */
    public function testAction(Request $request)
    {
        return $this->render('public/test.html.twig', ['no_hide' => true, 'js' => ['test' => '/vendor/release/app/test.js']]);
    }
}
