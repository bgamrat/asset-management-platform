<?php

namespace Common\AdminBundle\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\AppBundle\Form\Admin\User\InvitationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Common\AppBundle\Entity\Invitation;

class InvitationController extends Controller
{
    /**
     * @Route("/admin/user/invitation")
     * @Method("GET")
     */
    public function invitationAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
      
        return $this->render( 'admin/user/invitation/index.html.twig', array(
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }
}