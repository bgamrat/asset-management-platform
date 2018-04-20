<?php

Namespace App\Controller\Admin\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Common\PersonType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Description of PersonController
 *
 * @author bgamrat
 */
class PersonController extends Controller
{

    /**
     * @Route("/admin/common/person")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $personForm = $this->createForm( PersonType::class, null, [] );

        return $this->render( 'admin/common/people.html.twig', [
                    'title' => 'common.people',
                    'person_form' => $personForm->createView()] );
    }

}
