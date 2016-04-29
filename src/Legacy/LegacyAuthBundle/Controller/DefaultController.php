<?php

namespace Legacy\LegacyAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('LegacyAppBundle:Default:index.html.twig');
    }
}
