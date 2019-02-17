<?php

Namespace App\Controller\Admin\Common;

use App\Entity\Common\PersonType;
use App\Form\Admin\Common\PersonTypesType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of PersonTypeController
 *
 * @author bgamrat
 */
class PersonTypeController extends AbstractController
{

    /**
     * @Route("/admin/common/person-type", methods={"GET"})
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $personTypes = [];
        $personTypes['types'] = $em->getRepository( 'App\Entity\Common\PersonType' )->findAll();
        $personTypesForm = $this->createForm( PersonTypesType::class, $personTypes, 
                [ 'action' => $this->generateUrl( 'app_admin_common_persontype_save' )] );
        return $this->render( 'admin/common/person-types.html.twig', array(
                    'person_types_form' => $personTypesForm->createView())
                 );
    }

    /**
     * @Route("/admin/common/person-type/save", methods={"POST"})
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_SUPER_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $personTypes = [];
        $personTypes['types'] = $em->getRepository( 'App\Entity\Common\PersonType' )->findAll();
        $ids = [];
        foreach ($personTypes['types'] as $pt) {
            $ids[$pt->getId()] = $pt;
        }
        $form = $this->createForm( PersonTypesType::class, $personTypes, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $personTypes = $form->getData();
            foreach( $personTypes['types'] as $type )
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
                    'notice', 'common.success' );
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_common_persontype_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
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
            return $this->render( 'admin/common/person-types.html.twig', array(
                        'person_types_form' => $form->createView()
                    ) );
        }
    }

}
