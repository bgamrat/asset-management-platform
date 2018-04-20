<?php

Namespace App\Controller\Admin\Asset;

use App\Entity\Asset\IssueType;
use App\Form\Admin\Asset\IssueTypesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of IssueTypeController
 *
 * @author bgamrat
 */
class IssueTypeController extends Controller
{

    /**
     * @Route("/admin/asset/issue-type")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $issueTypes = [];
        $issueTypes['types'] = $em->getRepository( 'App\Entity\Asset\IssueType' )->findAll();
        $issueTypesForm = $this->createForm( IssueTypesType::class, $issueTypes, [ 'action' => $this->generateUrl( 'app_admin_asset_issuetype_save' )] );
        return $this->render( 'admin/asset/issue-types.html.twig', array(
                    'issue_types_form' => $issueTypesForm->createView())
                 );
    }

    /**
     * @Route("/admin/asset/issue-type/save")
     * @Method("POST")
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $issueTypes = [];
        $issueTypes['types'] = $em->getRepository( 'App\Entity\Asset\IssueType' )->findAll();
        $form = $this->createForm( IssueTypesType::class, $issueTypes, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $issueTypes = $form->getData();
            foreach( $issueTypes['types'] as $type )
            {
                $em->persist( $type );
            }
            $em->flush();
            $this->addFlash(
                    'notice', 'common.success' );
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_asset_issuetype_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
            $response->prepare( $request );

            return $response->send();
        }
        else
        {
            $errorMessages = [];
            $formData = $form->all();
            foreach( $formData as $name => $item )
            {
                if( !$item->isValid() )
                {
                    $errorMessages[] = $name . ' - ' . $item->getErrors( true );
                }
            }
            return $this->render( 'admin/asset/issue-types.html.twig', array(
                        'issue_types_form' => $form->createView()
                    ) );
        }
    }

}
