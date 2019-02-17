<?php

Namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LegacyBridgeController extends AbstractController
{
    public function indexAction()
    {
        return $this->render('LegacyApp\:Default:index.html.twig');
    }
}
