<?php

Namespace App\Controller\Admin\Asset;

use App\Entity\Asset\Category;
use App\Form\Admin\Asset\SetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of SetController
 *
 * @author bgamrat
 */
class SetsController extends AbstractController
{

    /**
     * @Route("/admin/asset/sets")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $sets = [];
        $sets['sets'] = $em->getRepository( 'App\Entity\Asset\Set' )->findAll();
        $form = $this->createForm( SetType::class, $sets );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $sets = $form->getData();
            $em->persist( $sets );

            $this->addFlash(
                    'notice', 'common.success' );
            return $this->redirectToRoute('admin_asset_set_index');
        }
        else
        {
            return $this->render( 'admin/asset/sets.html.twig', [
                        'set_form' => $form->createView()] );
        }
    }

}
