<?php

namespace AppBundle\Controller\Admin\Staff;

use AppBundle\Form\Admin\Staff\RoleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of RoleController
 *
 * @author bgamrat
 */
class RoleController extends Controller
{

    /**
     * @Route("/admin/staff/role")
     * @Route("/admin/staff/role/{name}", name="app_admin_staff_role_get")
     * @Method("GET")
     */
    public function indexAction( $name = null )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        if( $name !== null )
        {
            $role = $this->getDoctrine()->getEntityManager()->getRepository( 'AppBundle\Entity\Staff\Role' )->findOneBy( ['name' => $name] );
            $roleId = $role->getId();
        }
        else
        {
            $roleId = null;
        }

        $form = $this->createForm( RoleType::class, null, [] );

        return $this->render( 'admin/staff/role.html.twig', array(
                    'role_id' => $roleId,
                    'role_form' => $form->createView()) );
    }

    /**
     * @Route("/admin/staff/role/save")
     * @Method("POST")
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $roles = [];
        $roles['roles'] = $em->getRepository( 'AppBundle\Entity\Staff\Role' )->findAll();
        $ids = [];
        foreach ($roles['roles'] as $pt) {
            $ids[$pt->getId()] = $pt;
        }
        $form = $this->createForm( RolesType::class, $roles, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $roles = $form->getData();
            foreach( $roles['roles'] as $role )
            {
                if ($role !== null) {
                    unset($ids[$role->getId()]);
                    $em->persist( $role );
                }
            }
            foreach ($ids as $pt) {
                $em->remove($pt);
            }
            $em->flush();
            $this->addFlash(
                    'notice', 'staff.success' );
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_staff_role_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
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
            return $this->render( 'admin/staff/roles.html.twig', array(
                        'roles_form' => $form->createView()
                    ) );
        }
    }

}
