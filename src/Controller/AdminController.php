<?php

Namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Admin\User\InvitationType;
use App\Form\Admin\User\UserType;
use App\Form\Common\PersonType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Invitation;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin")
     */
    public function indexAction()
    {
        return $this->render( 'admin/index.html.twig' );
    }

}
