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
                ->from( 'AppBundle:Asset', 'a' )
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
            $location = $asset->getLocation();
            $data = [
                'id' => $asset->getId(),
                'model_text' => $brand->getName() . ' ' . $model->getName(),
                'model' => $model->getId(),
                'serial_number' => $asset->getSerialNumber(),
                'location_text' => $location->getName(),
                'location' => $location->getId(),
                'barcodes' => $asset->getBarcodes(),
                'comment' => $asset->getComment(),
                'active' => $asset->isActive()
            ];

            $columns = ['al.version AS version', 'al.action AS action', 'al.loggedAt AS timestamp', 'al.username AS username', 'al.data AS data'];
            $queryBuilder = $em->createQueryBuilder();
            $queryBuilder->select( $columns )
                    ->from( 'AppBundle:AssetLog', 'al' )
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
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $formProcessor = $this->get( 'app.util.form' );
        $data = $request->request->all();
        $asset = $em->getRepository( 'AppBundle:Asset' )->find( $id );
        $form = $this->createForm( AssetType::class, $asset, ['allow_extra_fields' => true] );
        try
        {
            $formProcessor->validateFormData( $form, $data );
            $form->handleRequest( $request );
            if( $form->isValid() )
            {
                $asset = $form->getData();
                $em->persist( $asset );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_assets_get_asset', array('id' => $asset->getId()), true // absolute
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
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $asset = $em->getRepository( 'AppBundle:Asset' )->find( $id );
        if( $asset !== null )
        {
            $em->getFilters()->enable( 'softdeleteable' );
            $em->remove( $asset );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }
}
