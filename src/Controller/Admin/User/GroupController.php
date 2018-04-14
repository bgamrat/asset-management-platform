<?php

Namespace App\Controller\Admin\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Form\Admin\User\GroupType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Description of DefaultController
 *
 * @author bgamrat
 */
class GroupController extends Controller
{

    /**
     * @Route("/admin/user/group/")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_GROUP', null, 'Unable to access this page!' );

        $group_form = $this->createForm( GroupType::class, null, [] );

        $em = $this->getDoctrine()->getManager();

        return $this->render( 'admin/user/group.html.twig', array(
                    'group_form' => $group_form->createView()) );
    }

}
