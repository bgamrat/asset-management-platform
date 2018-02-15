<?php

namespace AppBundle\Controller\Admin\Staff;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Description of DefaultController
 *
 * @author bgamrat
 */
class StaffController extends Controller
{

    /**
     * @Route("/admin/staff/index")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN_USER', null, 'Unable to access this page!' );


        return $this->render( 'admin/staff/index.html.twig' );
    }

}
