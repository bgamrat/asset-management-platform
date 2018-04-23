<?php

Namespace App\Controller\Api\Admin\Asset;

use App\Entity\Category;
use App\Util\DStore;
use App\Util\Form as FormUtil;
use App\Form\Admin\Asset\CategoryType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class CategoriesController extends FOSRestController
{

    private $dstore;
    private $formUtil;

    public function __construct( DStore $dstore, FormUtil $formUtil )
    {
        $this->dstore = $dstore;
        $this->formUtil = $formUtil;
    }

    /**
     * @View()
     */
    public function getCategoriesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder()->select( ['c.id', 'c.position', 'c.name', 'p.id AS parent'] )
                ->from( 'App\Entity\Asset\Category', 'c' )
                ->leftJoin('c.parent', 'p')
                ->orderBy( 'c.position' );

        $data = $queryBuilder->getQuery()->getResult();
        return $data;
    }

    /**
     * @View()
     */
    public function getCategoryAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $category = $this->getDoctrine()
                        ->getRepository( 'App\Entity\Asset\Category' )->find( $id );
        if( $category !== null )
        {
            return $category;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @View()
     */
    public function postCategoryAction( $id, Request $request )
    {
        return $this->putCategoryAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putCategoryAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $data = $request->request->all();
        if( $id === "null" )
        {
            $category = new Category();
        }
        else
        {
            $category = $em->getRepository( 'App\Entity\Asset\Category' )->find( $id );
        }
        $form = $this->createForm( CategoryType::class, $category, ['allow_extra_fields' => true] );
        try
        {
            $form->submit( $data );
            if( $form->isValid() )
            {
                $category = $form->getData();
                $em->persist( $category );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_categories_get_category', array('id' => $category->getId()), true // absolute
                        )
                );
            }
            else
            {
                return $form;
            }
        }
        catch( Exception $e )
        {
            $response->setStatusCode( 400 );
            $response->setContent( json_encode(
                            ['message' => 'errors', 'errors' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTraceAsString()]
            ) );
        }
        return $response;
    }

    /**
     * @View(statusCode=204)
     */
    public function patchCategoryAction( $id, Request $request )
    {
        $formProcessor = $this->formUtil;
        $data = $formProcessor->getJsonData( $request );
        $repository = $this->getDoctrine()
                ->getRepository( 'App\:Category' );
        $category = $repository->find( $id );
        if( $category !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $category->setActive( $value );
                        break;
                }

                $em->persist( $category );
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteCategoryAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->enable( 'softdeleteable' );
        $category = $em->getRepository( 'App\Entity\Asset\Category' )->find( $id );
        if( $category !== null )
        {
            $em->remove( $category );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

}
