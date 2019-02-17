<?php

Namespace App\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Admin\Asset\IssueType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of IssueController
 *
 * @author bgamrat
 */
class IssueController extends AbstractController
{

    /**
     * @Route("/admin/asset/issues", methods={"GET"})
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
