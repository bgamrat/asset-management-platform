<?php

Namespace App\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Admin\Asset\IssueType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Description of IssueController
 *
 * @author bgamrat
 */
class IssueController extends Controller
{

    /**
     * @Route("/admin/asset/issues")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $issueForm = $this->createForm( IssueType::class, null, [] );

        return $this->render( 'admin/asset/issues.html.twig', array(
                    'issue_form' => $issueForm->createView()
                ) );
    }

}
