<?php

Namespace App\Controller\Admin\Staff;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Common\PersonType;

/**
 * Description of DefaultController
 *
 * @author bgamrat
 */
class StaffController extends AbstractController
{

    /**
     * @Route("/admin/staff/index", methods={"GET"})
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
