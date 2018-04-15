<?php

Namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LegacyBridgeController extends Controller
{
    public function indexAction()
    {
        return $this->render('LegacyApp\:Default:index.html.twig');
    }
}
