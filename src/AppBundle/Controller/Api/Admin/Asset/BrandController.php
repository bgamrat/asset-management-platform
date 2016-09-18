<?php

namespace AppBundle\Controller\Api\Admin\Brand;

use AppBundle\Util\DStore;
use AppBundle\Entity\Brand;
use AppBundle\Form\Admin\Brand\BrandType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BrandsController extends FOSRestController
{

    /**
     * @View()
     */
    public function getBrandsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'id' );

        switch( $dstore['sort-field'] )
        {
            case 'barcode':
                $sortField = 'c.barcode';
                break;
            case 'location':
                $sortField = 'l.name';
                break;
            case 'brand':
                $sortField = 'b.name';
                break;
            case 'model':
                $sortField = 'm.name';
                break;
            default:
                $sortField = 'a.' . $dstore['sort-field'];
        }

        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $columns = ['a.id', 'l.name AS location_text', 'l.id AS location', 'bc.barcode', 'bc.updated AS barcode_updated', "CONCAT(CONCAT(b.name,' '),m.name) AS model_text", 'm.id AS model', 'a.serialNumber AS serial_number', 'a.comment', 'a.active'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 'a.deletedAt AS deleted_at';
        }
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle:Brand', 'a' )
                ->innerJoin( 'a.model', 'm' )
                ->innerJoin( 'm.brand', 'b' )
                ->leftJoin( 'a.barcodes', 'bc' )
                ->leftJoin( 'a.location', 'l' )
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
                                    $queryBuilder->expr()->like( 'a.model', '?1' ), $queryBuilder->expr()->like( 'a.serial_number', '?1' ) )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( 'a.model', '?1' )
                    );
            }
            $queryBuilder->setParameter( 1, $dstore['filter'][DStore::VALUE] );
        }
        $queryBuilder->andWhere( $queryBuilder->expr()->eq( 'bc.active', $queryBuilder->expr()->literal( true ) ) );
        $data = $queryBuilder->getQuery()->getResult();
        return array_values( $data );
    }

    /**
     * @View()
     */
    public function getBrandAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $repository = $this->getDoctrine()
                ->getRepository( 'AppBundle:Brand' );
        $brand = $repository->find( $id );
        if( $brand !== null )
        {
            $model = $brand->getModel();
            $brand = $model->getBrand();
            $location = $brand->getLocation();
            $data = [
                'id' => $brand->getId(),
                'model_text' => $brand->getName() . ' ' . $model->getName(),
                'model' => $model->getId(),
                'serial_number' => $brand->getSerialNumber(),
                'location_text' => $location->getName(),
                'location' => $location->getId(),
                'barcodes' => $brand->getBarcodes(),
                'comment' => $brand->getComment(),
                'active' => $brand->isActive()
            ];

            $columns = ['al.version AS version', 'al.action AS action', 'al.loggedAt AS timestamp', 'al.username AS username', 'al.data AS data'];
            $queryBuilder = $em->createQueryBuilder();
            $queryBuilder->select( $columns )
                    ->from( 'AppBundle:BrandLog', 'al' )
                    ->where( $queryBuilder->expr()->eq( 'al.objectId', '?1' ) );
            $queryBuilder->setParameter( 1, $id )->orderBy( 'al.loggedAt', 'desc' );
            $history = $queryBuilder->getQuery()->getResult();
            $logUtil = $this->get( 'app.util.log' );
            $logUtil->init( $history );
            $data['history'] = $logUtil->translateIdsToText();

            return $data;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @View()
     */
    public function postBrandAction( $id, Request $request )
    {
        return $this->putBrandAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putBrandAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $brand = $em->getRepository( 'AppBundle:Brand' )->find( $id );
        $form = $this->createForm( BrandType::class, $brand, ['allow_extra_fields' => true] );
        try
        {
            $formProcessor->validateFormData( $form, $data );
            $form->handleRequest( $request );
            if( $form->isValid() )
            {
                $brand = $form->getData();
                $em->persist( $brand );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_brands_get_brand', array('id' => $brand->getId()), true // absolute
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
    public function patchBrandAction( $id, Request $request )
    {
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'AppBundle:Brand' );
        $brand = $repository->find( $id );
        if( $brand !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $brand->setActive( $value );
                        break;
                }

                $em->persist( $brand );
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteBrandAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $brand = $em->getRepository( 'AppBundle:Brand' )->find( $id );
        if( $brand !== null )
        {
            $em->getFilters()->enable( 'softdeleteable' );
            $em->remove( $brand );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @Route("/api/brands/{name}/brands")
     * @Method("GET")
     * @View()
     */
    public function getBrandBrandsAction( $name, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $repository = $this->getDoctrine()
                ->getRepository( 'AppBundle:Brand' );
        $brand = $repository->findOneBy( ['name' => $name] );
        if( $brand !== null )
        {
            $data = [];
            $data['brands'] = $brand->getBrands();
            return $data;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @Route("/api/brands/{mname}/brand/{bname}/models")
     * @Method("GET")
     * @View()
     */
    public function getBrandBrandModelsAction( $mname, $bname, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery( 'SELECT m, b, d FROM AppBundle\Entity\Brand m JOIN m.brands b JOIN b.models d WHERE m.name = :mname AND b.name = :bname' );
        $query->setParameters( ['mname' => $mname, 'bname' => $bname] );
        $brand = $query->getResult();
        if( $brand !== null )
        {
            $data = [];
            if( count( $brand ) > 0 )
            {
                $brands = $brand[0]->getBrands();
                if( count( $brands ) > 0 )
                {
                    $data['models'] = $brands[0]->getModels();
                }
            }
            return $data;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @Route("/api/brands/{mname}/brand/{bname}/models")
     * @Method("POST")
     * @View()
     */
    public function postBrandBrandModelsAction( $mname, $bname, Request $request )
    {
        return $this->putBrandBrandModelsAction( $mname, $bname, $request );
    }

    /**
     * @Route("/api/brands/{mname}/brand/{bname}/models")
     * @Method("PUT")
     * @View()
     */
    public function putBrandBrandModelsAction( $mname, $bname, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $response = new Response();
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $form = $this->createForm( ModelsType::class, null, [] );
        try
        {
            $formProcessor->validateFormData( $form, $data );
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery( 'SELECT m, b FROM AppBundle\Entity\Brand m JOIN m.brands b WHERE m.name = :mname AND b.name = :bname' );
            $query->setParameters( ['mname' => $mname, 'bname' => $bname] );
            $brand = $query->getResult();
            $brand = $brand[0]->getBrands()[0];
            if( $brand !== null )
            {
                $modelUtil = $this->get( 'app.util.model' );
                $modelUtil->update( $brand, $data['models'] );
                $em->persist( $brand );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_brands_get_brand_brand_models', array('mname' => $mname, 'bname' => $bname), true // absolute
                        )
                );
            }
            else
            {
                throw $this->createNotFoundException( 'Not found!' );
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

}
