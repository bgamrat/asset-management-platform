<?php

Namespace App\Controller\Admin\Asset;

use App\Entity\Category;
use Form\Admin\Asset\CategoriesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

        $em = $this->getDoctrine()->getManager();
        $categories = [];
        $categories['categories'] = $em->getRepository( 'Entity\Asset\Category' )->findAll();
        $categoriesForm = $this->createForm( CategoriesType::class, $categories, [ 'action' => $this->generateUrl( 'app_admin_asset_category_save' )] );
        return $this->render( 'admin/asset/categories.html.twig', array(
                    'categories_form' => $categoriesForm->createView()
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
        $categories = [];
        $categories['categories'] = $em->getRepository( 'Entity\Asset\Category' )->findAll();
        $form = $this->createForm( CategoriesType::class, $categories, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $categories = $form->getData();
            foreach( $categories['categories'] as $category )
            {
                $em->persist( $category );
            }

            $categories = $em->getRepository( 'Entity\Asset\Category' )->findAll();
            foreach( $categories as $category )
            {
                $category->setFullName();
                $em->persist( $category );
            }
            $em->flush();

            $this->addFlash(
                    'notice', 'common.success' );
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_asset_category_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
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
            return $this->render( 'admin/asset/categories.html.twig', array(
                        'categories_form' => $form->createView()) );
        }
    }

}
