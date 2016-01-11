<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Model\GroupManager as GroupManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Group;
use AppBundle\Form\Admin\User\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminController extends Controller
{
    /**
     * @Route("/admin")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $groups = $this->get( 'fos_user.group_manager' )->findGroups();
        $groupNames = [ ];
        foreach ( $groups as $g )
        {
            $groupNames[$g->getName()] = $g->getId();
        }

        $user = new User();
        $user_form = $this->createForm( UserType::class, $user, ['groups' => $groupNames ] );

        return $this->render( 'admin/index.html.twig', array(
                    'user_form' => $user_form->createView(),
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }
    
    public function getAdminction ( $slug ) {
        // Supports routing
    }
}