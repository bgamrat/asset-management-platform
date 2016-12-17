<?php

namespace AppBundle\Controller\Api\Admin\Asset;

use AppBundle\Util\DStore;
use AppBundle\Entity\Asset\Manufacturer;
use AppBundle\Entity\Asset\Model;
use AppBundle\Form\Admin\Asset\ManufacturerType;
use AppBundle\Form\Admin\Asset\ModelType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
        $queryBuilder = $em->createQueryBuilder()->select( 'm' )
                ->from( 'AppBundle\Entity\Asset\Manufacturer', 'm' )
                ->leftJoin( 'm.brands', 'b' )
                ->leftJoin( 'm.contacts', 'c' )
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
        $manufacturers = $queryBuilder->getQuery()->getResult();
        return $manufacturers;
    }

    /**
     * @View()
     */
    public function getManufacturerAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $repository = $this->getDoctrine()
                ->getRepository( 'AppBundle\Entity\Asset\Manufacturer' );
        $manufacturer = $repository->find( $id );
        if( $manufacturer !== null )
        {
            $data = [
                'id' => $manufacturer->getId(),
                'name' => $manufacturer->getName(),
                'comment' => $manufacturer->getComment(),
                'brands' => $manufacturer->getBrands(false),
                'contacts' => $manufacturer->getContacts(false),
                'active' => $manufacturer->isActive()
            ];
            $formUtil = $this->get( 'app.util.form' );
            $formUtil->saveDataTimestamp( 'manufacturer' . $manufacturer->getId(), $manufacturer->getUpdated() );
            return $data;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     */
    public function postManufacturerAction( $id, Request $request )
    {
        return $this->putManufacturerAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putManufacturerAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $data = $request->request->all();
        if( $id === 'null' )
        {
            $manufacturer = new Manufacturer();
        }
        else
        {
            $manufacturer = $em->getRepository( 'AppBundle\Entity\Asset\Manufacturer' )->find( $id );
            $formUtil = $this->get( 'app.util.form' );
            if( $formUtil->checkDataTimestamp( 'manufacturer' . $manufacturer->getId(), $manufacturer->getUpdated() ) === false )
            {
                throw new Exception( "data.outdated", 400 );
            }
        }

        $form = $this->createForm( ManufacturerType::class, $manufacturer, ['allow_extra_fields' => true] );
        $form->submit( $data );

        if( $form->isValid() )
        {
            $manufacturer = $form->getData();
            $em->persist( $manufacturer );
            $em->flush();
            $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
            $response->headers->set( 'Location', $this->generateUrl(
                            'app_admin_api_manufacturers_get_manufacturer', array('id' => $manufacturer->getId()), true // absolute
            ) );

            return $response;
        }
        
        $response->setStatusCode( 400 );

        return $form;
    }

    /**
     * @View(statusCode=204)
     */
    public function patchManufacturerAction( $name, Request $request )
    {
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'AppBundle\Entity\Asset\Manufacturer' );
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
        $manufacturer = $em->getRepository( 'AppBundle\Entity\Asset\Manufacturer' )->findOneBy( ['name' => $name] );
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
                ->getRepository( 'AppBundle\Entity\Asset\Manufacturer' );
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
            case 'category_text':
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
        $columns = ['m.id', 'c.fullName AS category_text', 'm.container', 'm.name AS name', 'm.comment', 'm.active'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 'm.deletedAt AS deleted_at';
        }
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle\Entity\Asset\Model', 'm' )
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
        $columns = ['m.id AS model_id', 'mn.id', 'b.id'];
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle\Entity\Asset\Manufacturer', 'mn' )
                ->innerJoin( 'mn.brands', 'b' )
                ->innerJoin( 'b.models', 'm' );
        $queryBuilder->where(
                        $queryBuilder->expr()->andX(
                                $queryBuilder->expr()->eq( 'mn.name', '?1' ), $queryBuilder->expr()->eq( 'b.name', '?2' ) ) )
                ->andWhere( $queryBuilder->expr()->eq( 'm.name', '?3' ) );
        $queryBuilder->setParameters( [1 => $mnname, 2 => $bname, 3 => $mname] );
        $data = $queryBuilder->getQuery()->getResult();

        if( !empty( $data ) )
        {
            $model = $em->getRepository( 'AppBundle\Entity\Asset\Model' )->find( $data[0]['model_id'] );
            $modelData = $model->toArray();
            $modelData['category'] = $model->getCategory()->getId();
            $modelData['category_text'] = $model->getCategory()->getName();
            $logUtil = $this->get( 'app.util.log' );
            $logUtil->getLog( 'AppBundle\Entity\Asset\ModelLog', $model->getId() );
            $modelData['history'] = $logUtil->translateIdsToText();
            $formUtil = $this->get( 'app.util.form' );
            $formUtil->saveDataTimestamp( 'model' . $model->getId(), $model->getUpdated() );
            return $modelData;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @Route("/api/manufacturers/{mnname}/brands/{bname}/models/{mname}")
     * @Method("POST")
     * @View()
     */
    public function postManufacturerBrandModelsAction( $mnname, $bname, $mname, Request $request )
    {
        return $this->putManufacturerBrandModelsAction( $mnname, $bname, $mname, $request );
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

        $columns = [ 'b.id AS brand_id', 'mn.id AS manufacturer_id'];
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle\Entity\Asset\Manufacturer', 'mn' )
                ->innerJoin( 'mn.brands', 'b' );
        $queryBuilder->where(
                $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq( 'mn.name', '?1' ), $queryBuilder->expr()->eq( 'b.name', '?2' ) ) );
        $queryBuilder->setParameters( [1 => $mnname, 2 => $bname] );
        $manufacturerAndBrandData = $queryBuilder->getQuery()->getResult();
        if( $mname !== "null" )
        {
            $queryBuilder->addSelect( 'm.id AS model_id' )
                    ->join( 'b.models', 'm' )
                    ->andWhere( $queryBuilder->expr()->orX( $queryBuilder->expr()->eq( 'm.name', '?3' ), $queryBuilder->expr()->eq( 'm.id', '?4' ) ) )
                    ->setParameter( 3, $mname )
                    ->setParameter( 4, $request->get( 'id' ) );
        }

        $modelData = $queryBuilder->getQuery()->getResult();

        if( !empty( $manufacturerAndBrandData ) )
        {
            if( isset( $modelData[0]['model_id'] ) )
            {
                $model = $em->getRepository( 'AppBundle\Entity\Asset\Model' )->find( $modelData[0]['model_id'] );
                $formUtil = $this->get( 'app.util.form' );
                if( $formUtil->checkDataTimestamp( 'model' . $model->getId(), $model->getUpdated() ) === false )
                {
                    throw new \Exception( "data.outdated", 400 );
                }
            }
            else
            {
                $model = new Model();
            }
            $brand = $em->getRepository( 'AppBundle\Entity\Asset\Brand' )->find( $manufacturerAndBrandData[0]['brand_id'] );
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }

        $form = $this->createForm( ModelType::class, $model, ['allow_extra_fields' => true] );
        $data = $request->request->all();
        $form->submit( $data );

        if( $form->isValid() )
        {
            $model = $form->getData();
            $model->setBrand( $brand );
            $brand->addModel( $model );
            $em->persist( $model );
            $em->persist( $brand );
            $em->flush();
            $response->setStatusCode( $mname === 'null' ? 201 : 204  );
            $response->headers->set( 'Location', $this->generateUrl(
                            'app_admin_api_manufacturers_get_manufacturers_brands_model', ['mnname' => $mnname, 'bname' => $bname, 'mname' => $model->getId()], true // absolute
                    )
            );
            return $response;
        }

        return $form;
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
                ->from( 'AppBundle\Entity\Asset\Manufacturer', 'mn' )
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
            $model = $em->getRepository( 'AppBundle\Entity\Asset\Model' )->find( $modelData[0]['model_id'] );
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
