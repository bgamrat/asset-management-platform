<?php

namespace AppBundle\Controller\Admin\Staff;

use AppBundle\Entity\Staff\RoleType;
use AppBundle\Form\Admin\Staff\RoleTypesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of RoleTypeController
 *
 * @author bgamrat
 */
class RoleTypeController extends Controller
{

    /**
     * @Route("/admin/staff/role-type")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $roleTypes = [];
        $roleTypes['types'] = $em->getRepository( 'AppBundle\Entity\Staff\RoleType' )->findAll();
        $roleTypesForm = $this->createForm( RoleTypesType::class, $roleTypes, 
                [ 'action' => $this->generateUrl( 'app_admin_staff_roletype_save' )] );
        return $this->render( 'admin/staff/role-types.html.twig', array(
                    'role_types_form' => $roleTypesForm->createView())
                 );
    }

    /**
     * @Route("/admin/staff/role-type/save")
     * @Method("POST")
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $roleTypes = [];
        $roleTypes['types'] = $em->getRepository( 'AppBundle\Entity\Staff\RoleType' )->findAll();
        $ids = [];
        foreach ($roleTypes['types'] as $pt) {
            $ids[$pt->getId()] = $pt;
        }
        $form = $this->createForm( RoleTypesType::class, $roleTypes, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $roleTypes = $form->getData();
            foreach( $roleTypes['types'] as $type )
            {
                if ($type !== null) {
                    unset($ids[$type->getId()]);
                    $em->persist( $type );
                }
            }
            foreach ($ids as $pt) {
                $em->remove($pt);
            }
            $em->flush();
            $this->addFlash(
                    'notice', 'staff.success' );
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_staff_roletype_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
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
            return $this->render( 'admin/staff/role-types.html.twig', array(
                        'role_types_form' => $form->createView()
                    ) );
        }
    }

}
