<?php

namespace AppBundle\Controller\Api\Admin\Asset;

use AppBundle\Util\DStore;
use AppBundle\Entity\Manufacturer;
use AppBundle\Entity\Model;
use AppBundle\Form\Admin\Asset\BrandsType;
use AppBundle\Form\Admin\Asset\ManufacturerType;
use AppBundle\Form\Admin\Asset\ModelType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Util\Model As ModelUtil;
use AppBundle\Repository\BrandRepository;

class ManufacturersController extends FOSRestController
{

    /**
     * @View()
     */
    public function getManufacturersAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'name' );

        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $queryBuilder = $em->createQueryBuilder()->select( ['m'] )
                ->from( 'AppBundle:Manufacturer', 'm' )
                ->orderBy( 'm.' . $dstore['sort-field'], $dstore['sort-direction'] );
        if( $dstore['limit'] !== null )
        {
            $queryBuilder->setMaxResults( $dstore['limit'] );
        }
        if( $dstore['offset'] !== null )
        {
            $queryBuilder->setFirstResult( $dstore['offset'] );
        }
        if( $dstore['filter'] !== null )
        {
            switch( $dstore['filter'][DStore::OP] )
            {
                case DStore::LIKE:
                    $queryBuilder->where(
                            $queryBuilder->expr()->orX(
                                    $queryBuilder->expr()->like( 'm.name', '?1' ), $queryBuilder->expr()->like( 'u.email', '?1' ) )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( 'm.name', '?1' )
                    );
            }
            $queryBuilder->setParameter( 1, $dstore['filter'][DStore::VALUE] );
        }
        $query = $queryBuilder->getQuery();
        $manufacturerCollection = $query->getResult();
        $data = [];
        $personModel = $this->get( 'app.model.person' );
        foreach( $manufacturerCollection as $m )
        {
            // TODO: Add full multi-contact support
            $contacts = $m->getContacts();
            $item = [
                'name' => $m->getName(),
                'contacts' => [$personModel->get( $contacts[0] )],
                'brands' => $m->getBrands(),
                'active' => $m->isActive(),
            ];
            if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
            {
                $item['deleted_at'] = $m->getDeletedAt();
            }
            $data[] = $item;
        }
        return $data;
    }

    /**
     * @View()
     */
    public function getManufacturerAction( $name )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $repository = $this->getDoctrine()
                ->getRepository( 'AppBundle:Manufacturer' );
        $manufacturer = $repository->findOneBy( ['name' => $name] );
        if( $manufacturer !== null )
        {
            $personModel = $this->get( 'app.model.person' );
            // TODO: Add full multi-contact support
            $contacts = $manufacturer->getContacts();
            $data = [
                'name' => $manufacturer->getName(),
                'brands' => $manufacturer->getBrands(),
                'contacts' => [$personModel->get( $contacts[0] )],
                'active' => $manufacturer->isActive()
            ];

            return $data;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     */
    public function postManufacturerAction( $name, Request $request )
    {
        return $this->putManufacturerAction( $name, $request );
    }

    /**
     * @View()
     */
    public function putManufacturerAction( $name, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $form = $this->createForm( ManufacturerType::class, null, [] );
        $manufacturer = $em->getRepository( 'AppBundle:Manufacturer' )->find( [ 'name' => $name] );
        $form = $this->createForm( ManufacturerType::class, $manufacturer, ['allow_extra_fields' => true] );
        try
        {
            $formProcessor->validateFormData( $form, $data );
            $form->handleRequest( $request );
            if( $form->isValid() )
            {
                $manufacturer = $form->getData();
                $em->persist( $manufacturer );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_manufacturer_get_manufacturer', array('name' => $manufacturer->getName()), true // absolute
                        )
                );
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
    public function patchManufacturerAction( $name, Request $request )
    {
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'AppBundle:Manufacturer' );
        $manufacturer = $repository->findOneBy( ['name' => $name] );
        if( $manufacturer !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $manufacturer->setActive( $value );
                        break;
                }

                $em->persist( $manufacturer );
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteManufacturerAction( $name )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->enable( 'softdeleteable' );
        $manufacturer = $em->getRepository( 'AppBundle:Manufacturer' )->findOneBy( ['name' => $name] );
        if( $manufacturer !== null )
        {
            $em->remove( $manufacturer );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @Route("/api/manufacturers/{name}/brands")
     * @Method("GET")
     * @View()
     */
    public function getManufacturerBrandsAction( $name, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $repository = $this->getDoctrine()
                ->getRepository( 'AppBundle:Manufacturer' );
        $manufacturer = $repository->findOneBy( ['name' => $name] );
        if( $manufacturer !== null )
        {
            $data = [];
            $data['brands'] = $manufacturer->getBrands();
            return $data;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @Route("/api/manufacturers/{mname}/brands/{bname}/models")
     * @Method("GET")
     * @View()
     */
    public function getManufacturersBrandsModelsAction( $mname, $bname, Request $request )
    {

        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'id' );

        switch( $dstore['sort-field'] )
        {
            case 'category':
                $sortField = 'c.name';
                break;
            case 'model':
                $sortField = 'm.name';
                break;
            default:
                $sortField = 'm.' . $dstore['sort-field'];
        }

        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $columns = ['m.id', 'c.name AS category_text', 'm.name AS model', 'm.comment', 'm.active'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 'm.deletedAt AS deleted_at';
        }
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle:Model', 'm' )
                ->innerJoin( 'm.category', 'c' )
                ->innerJoin( 'm.brand', 'b' )
                ->orderBy( $sortField, $dstore['sort-direction'] );
        if( $dstore['limit'] !== null )
        {
            $queryBuilder->setMaxResults( $dstore['limit'] );
        }
        if( $dstore['offset'] !== null )
        {
            $queryBuilder->setFirstResult( $dstore['offset'] );
        }
        if( $dstore['filter'] !== null )
        {
            switch( $dstore['filter'][DStore::OP] )
            {
                case DStore::LIKE:
                    $queryBuilder->where(
                            $queryBuilder->expr()->orX(
                                    $queryBuilder->expr()->like( 'm.name', '?1' ), $queryBuilder->expr()->like( 'c.name', '?1' ) )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( 'm.name', '?1' )
                    );
            }
            $queryBuilder->setParameter( 1, $dstore['filter'][DStore::VALUE] );
        }
        $queryBuilder->andWhere( $queryBuilder->expr()->eq( 'b.name', '?2' ) );
        $queryBuilder->setParameter( 2, $bname );
        $data = $queryBuilder->getQuery()->getResult();
        return array_values( $data );
    }

    /**
     * @Route("/api/manufacturers/{mnname}/brands/{bname}/model/{mname}")
     * @Method("GET")
     * @View()
     */
    public function getManufacturersBrandsModelAction( Request $request, $mnname, $bname, $mname )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $response = new Response();
        $em = $this->getDoctrine()->getManager();
        $columns = ['m', 'mn', 'b', 'c'];
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle:Manufacturer', 'mn' )
                ->innerJoin( 'mn.brands', 'b' )
                ->innerJoin( 'b.models', 'm' )
                ->innerJoin( 'm.category', 'c' );
        $queryBuilder->where(
                        $queryBuilder->expr()->andX(
                                $queryBuilder->expr()->eq( 'mn.name', '?1' ), $queryBuilder->expr()->eq( 'b.name', '?2' ) ) )
                ->andWhere( $queryBuilder->expr()->eq( 'm.name', '?3' ) );
        $queryBuilder->setParameters( [1 => $mnname, 2 => $bname, 3 => $mname] );
        $data = $queryBuilder->getQuery()->getResult();
        if( !empty( $data ) )
        {
            $manufacturer = $data[0];
            $brand = $manufacturer->getBrands();
            $models = $brand[0]->getModels();
            $model = $models[0];
            $modelData = $model->toArray();
            $modelData['category'] = $model->getCategory()->getId();
            $modelData['category_text'] = $model->getCategory()->getName();
            $logUtil = $this->get( 'app.util.log' );
            $logUtil->getLog( 'AppBundle:ModelLog', $model->getId() );
            $modelData['history'] = $logUtil->translateIdsToText();

            return $modelData;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @Route("/api/manufacturers/{mname}/brands/{bname}/models/{dname}")
     * @Method("POST")
     * @View()
     */
    public function postManufacturerBrandModelsAction( $mname, $bname, $dname, Request $request )
    {
        return $this->putManufacturerBrandModelsAction( $mname, $bname, $dname, $request );
    }

    /**
     * @Route("/api/manufacturers/{mnname}/brands/{bname}/models/{mname}")
     * @Method("PUT")
     * @View()
     */
    public function putManufacturerBrandModelsAction( Request $request, $mnname, $bname, $mname )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $response = new Response();
        $em = $this->getDoctrine()->getManager();

        $columns = ['m', 'mn', 'b', 'c'];
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle:Manufacturer', 'mn' )
                ->innerJoin( 'mn.brands', 'b' )
                ->innerJoin( 'b.models', 'm' )
                ->innerJoin( 'm.category', 'c' );
        $queryBuilder->where(
                        $queryBuilder->expr()->andX(
                                $queryBuilder->expr()->eq( 'mn.name', '?1' ), $queryBuilder->expr()->eq( 'b.name', '?2' ) ) )
                ->andWhere( $queryBuilder->expr()->eq( 'm.name', '?3' ) );
        $queryBuilder->setParameters( [1 => $mnname, 2 => $bname, 3 => $mname] );
        $data = $queryBuilder->getQuery()->getResult();

        if( !empty( $data ) )
        {
            $manufacturer = $data[0];
            $brand = $manufacturer->getBrands();
            $models = $brand[0]->getModels();
            $model = $models[0];
        }
        else
        {
            $model = new Model();
        }
        $form = $this->createForm( ModelType::class, $model, ['allow_extra_fields' => true] );
        $formProcessor = $this->get( 'app.util.form' );
        try
        {
            $formProcessor->validateFormData( $form, $request->request->all() );
            $form->handleRequest( $request );
            if( $form->isValid() )
            {
                $brand = $em->getRepository( 'AppBundle:Brand' )->findOneBy( ['name' => $bname] );
                $model = $form->getData();
                $brand->addModel( $model );
                $em->persist( $model );
                $em->persist( $brand );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_manufacturers_get_manufacturers_brands_model', ['mnname' => $mnname, 'bname' => $bname, 'mname' => $mname], true // absolute
                        )
                );
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
     * @Route("/api/manufacturers/{mnname}/brands/{bname}/models/{mname}")
     * @Method("PATCH")
     * @View(statusCode=204)
     */
    public function patchManufacturersBrandsModelsAction( $mnname, $bname, $mname, Request $request )
    {
        $formProcessor = $this->get( 'app.util.form' );
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $columns = ['m.id AS model_id'];
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle:Manufacturer', 'mn' )
                ->innerJoin( 'mn.brands', 'b' )
                ->innerJoin( 'b.models', 'm' );
        $queryBuilder->where(
                        $queryBuilder->expr()->andX(
                                $queryBuilder->expr()->eq( 'mn.name', '?1' ), $queryBuilder->expr()->eq( 'b.name', '?2' ) ) )
                ->andWhere( $queryBuilder->expr()->eq( 'm.name', '?3' ) );
        $queryBuilder->setParameters( [1 => $mnname, 2 => $bname, 3 => $mname] );
        $modelData = $queryBuilder->getQuery()->getResult();

        if( !empty( $modelData ) )
        {
            $model = $em->getRepository( 'AppBundle:Model' )->find( $modelData[0]['model_id'] );
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $model->setActive( $value );
                        break;
                }

                $em->persist( $model );
                $em->flush();
            }
        }
    }

}
