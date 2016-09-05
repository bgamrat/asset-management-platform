<?php

namespace AppBundle\Controller\Api\Admin\Asset;

use AppBundle\Util\DStore;
use AppBundle\Entity\Asset;
use AppBundle\Form\Admin\Asset\AssetType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class AssetsController extends FOSRestController
{

    /**
     * @View()
     */
    public function getAssetsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'id' );

        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $columns = ['a.id', "CONCAT(CONCAT(b.name,' '),m.name) AS model", 'a.serialNumber AS serial_number', 'a.comment', 'a.active'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 'a.deletedAt AS deleted_at';
        }
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle:Asset', 'a' )
                ->innerJoin( 'a.model', 'm' )
                ->innerJoin( 'm.brand', 'b' )
                ->orderBy( 'a.' . $dstore['sort-field'], $dstore['sort-direction'] );
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
        $data = $queryBuilder->getQuery()->getResult();
        return $data;
    }

    /**
     * @View()
     */
    public function getAssetAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $repository = $this->getDoctrine()
                ->getRepository( 'AppBundle:Asset' );
        $asset = $repository->find( $id );
        if( $asset !== null )
        {
            $model = $asset->getModel();
            $brand = $model->getBrand();
            $data = [
                'model' => $model->getName() . ' ' . $brand->getName(),
                'serial_number' => $asset->getSerialNumber(),
                'location' => $asset->getLocation(),
                'barcodes' => $asset->getBarcodes(),
                'comments' => $asset->getComment(),
                'active' => $asset->isActive()
            ];

            $columns = ['al.version AS version','al.loggedAt AS timestamp','al.username AS username','al.data AS data'];
            $queryBuilder = $em->createQueryBuilder();
            $queryBuilder->select( $columns )
                    ->from( 'AppBundle:AssetLog', 'al' )
                    ->where($queryBuilder->expr()->eq( 'al.objectId', '?1' ));
            $queryBuilder->setParameter( 1, $id );
            $history = $queryBuilder->getQuery()->getResult();
            $data['history'] = $history;

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
    public function postAssetAction( $id, Request $request )
    {
        return $this->putAssetAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putAssetAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $response = new Response();
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $form = $this->createForm( AssetType::class, null, [] );
        try
        {
            $formProcessor->validateFormData( $form, $data );
            $em = $this->getDoctrine()->getManager();
            $asset = $em->getRepository( 'AppBundle:Asset' )->find( $id );

            if( $asset === null )
            {
                $asset = new Asset();
            }
            $asset->setModel( $data['model'] );
            $asset->setSerialNumber( $data['serial_number'] );
            $asset->setComment( $data['comment'] );
            $asset->setActive( $data['active'] );
            $em->persist( $asset );
            $em->flush();

            $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
            $response->headers->set( 'Location', $this->generateUrl(
                            'app_admin_api_assets_get_asset', array('id' => $asset->getId()), true // absolute
                    )
            );
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
    public function patchAssetAction( $id, Request $request )
    {
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'AppBundle:Asset' );
        $asset = $repository->find( $id );
        if( $asset !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $asset->setActive( $value );
                        break;
                }

                $em->persist( $asset );
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteAssetAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->enable( 'softdeleteable' );
        $asset = $em->getRepository( 'AppBundle:Asset' )->find( $id );
        if( $asset !== null )
        {
            $em->remove( $asset );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @Route("/api/assets/{name}/brands")
     * @Method("GET")
     * @View()
     */
    public function getAssetBrandsAction( $name, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $repository = $this->getDoctrine()
                ->getRepository( 'AppBundle:Asset' );
        $asset = $repository->findOneBy( ['name' => $name] );
        if( $asset !== null )
        {
            $data = [];
            $data['brands'] = $asset->getBrands();
            return $data;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @Route("/api/assets/{mname}/brand/{bname}/models")
     * @Method("GET")
     * @View()
     */
    public function getAssetBrandModelsAction( $mname, $bname, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery( 'SELECT m, b, d FROM AppBundle\Entity\Asset m JOIN m.brands b JOIN b.models d WHERE m.name = :mname AND b.name = :bname' );
        $query->setParameters( ['mname' => $mname, 'bname' => $bname] );
        $asset = $query->getResult();
        if( $asset !== null )
        {
            $data = [];
            if( count( $asset ) > 0 )
            {
                $brands = $asset[0]->getBrands();
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
     * @Route("/api/assets/{mname}/brand/{bname}/models")
     * @Method("POST")
     * @View()
     */
    public function postAssetBrandModelsAction( $mname, $bname, Request $request )
    {
        return $this->putAssetBrandModelsAction( $mname, $bname, $request );
    }

    /**
     * @Route("/api/assets/{mname}/brand/{bname}/models")
     * @Method("PUT")
     * @View()
     */
    public function putAssetBrandModelsAction( $mname, $bname, Request $request )
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
            $query = $em->createQuery( 'SELECT m, b FROM AppBundle\Entity\Asset m JOIN m.brands b WHERE m.name = :mname AND b.name = :bname' );
            $query->setParameters( ['mname' => $mname, 'bname' => $bname] );
            $asset = $query->getResult();
            $brand = $asset[0]->getBrands()[0];
            if( $brand !== null )
            {
                $modelUtil = $this->get( 'app.util.model' );
                $modelUtil->update( $brand, $data['models'] );
                $em->persist( $brand );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_assets_get_asset_brand_models', array('mname' => $mname, 'bname' => $bname), true // absolute
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
