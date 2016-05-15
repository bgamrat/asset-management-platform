<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Admin\User\InvitationType;
use AppBundle\Form\Admin\User\UserType;
use AppBundle\Form\Common\PersonType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Invitation;

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
