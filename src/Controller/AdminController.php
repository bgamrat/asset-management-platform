<?php

Namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Admin\User\InvitationType;
use App\Form\Admin\User\UserType;
use App\Form\Common\PersonType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Entity\Invitation;

class AdminController extends Controller
{

    /**
     * @Route("/admin")
     */
    public function indexAction()
    {
        return $this->render( 'admin/index.html.twig' );
    }

}
