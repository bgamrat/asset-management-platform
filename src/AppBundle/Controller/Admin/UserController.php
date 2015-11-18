<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserManager as BaseUserManager;

class UserController extends Controller
{

    /**
     * @Route("/admin/user", name="adminuser")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        /**
         * Show all users
         */
        
        $users = $this->get( 'fos_user.user_manager' )->findUsers();

        return $this->render( 'admin/user/index.html.twig', array(
                    'users' => $users,
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
        ) );
    }

}
