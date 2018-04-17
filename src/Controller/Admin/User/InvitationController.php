<?php

Namespace App\Controller\Admin\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Form\Admin\User\InvitationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Entity\Invitation;

class InvitationController extends Controller
{
    /**
     * @Route("/admin/user/invitation")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
      
        return $this->render( 'admin/user/invitation/index.html.twig' );
    }
}