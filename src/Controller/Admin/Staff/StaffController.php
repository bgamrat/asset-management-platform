<?php

Namespace App\Controller\Admin\Staff;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Form\Common\PersonType;

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
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $personForm = $this->createForm( PersonType::class, null, [] );

        return $this->render( 'admin/common/people.html.twig',[
                    'title' => 'common.staff',
                    'person_form' => $personForm->createView()] );
    }

}
