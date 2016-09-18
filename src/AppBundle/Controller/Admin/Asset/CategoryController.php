<?php

namespace AppBundle\Controller\Admin\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Admin\Asset\CategoriesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Description of CategoryController
 *
 * @author bgamrat
 */
class CategoryController extends Controller
{

    /**
     * @Route("/admin/asset/category")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $repository = $this->getDoctrine()
                ->getRepository( 'AppBundle:Category' );
        $categories = $repository->findAll();

        $categoriesForm = $this->createForm( CategoriesType::class, $categories, [ 'action' => $this->generateUrl( 'app_admin_asset_category_save' )] );

        return $this->render( 'admin/asset/categories.html.twig', array(
                    'categories_form' => $categoriesForm->createView(),
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }

    /**
     * @Route("/admin/asset/category/save")
     * @Method("POST")
     */
    public function saveAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $categories = $em->getRepository( 'AppBundle:Category' )->findAll();
        $form = $this->createForm( CategoriesType::class, $categories, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $categories = $form->getData();
            $em->persist( $categories );
            $em->flush();
        }

        $this->redirectToRoute( 'app_admin_asset_category_index' );
    }

}
