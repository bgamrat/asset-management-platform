<?php

namespace AppBundle\Controller\Api\Admin\Asset;

use AppBundle\Util\DStore;
use AppBundle\Entity\Asset\Asset;
use AppBundle\Entity\Asset\Location;
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
                $sortField = 'bc.barcode';
                break;
            case 'location_text':
                $sortField = 'a.location_text';
                break;
            case 'brand':
                $sortField = 'b.name';
                break;
            case 'model':
            case 'model_text':
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
        $columns = ['a.id', 'a.location_text', 'bc.barcode', 'bc.updated AS barcode_updated',
            "CONCAT(CONCAT(b.name,' '),m.name) AS model_text", 'm.id AS model', 'a.serial_number',
            's.name AS status_text', 's.id AS status',
            'a.comment', 'a.active'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 'a.deletedAt AS deleted_at';
        }
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                ->innerJoin( 'a.model', 'm' )
                ->innerJoin( 'm.brand', 'b' )
                ->leftJoin( 'a.barcodes', 'bc', 'WITH', 'bc.active = true' )
                ->leftJoin( 'a.status', 's' )
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
                                    $queryBuilder->expr()->orX(
                                            $queryBuilder->expr()->like( "LOWER(CONCAT(CONCAT(b.name,' '),m.name))", '?1' ), 
                                            $queryBuilder->expr()->like( 'LOWER(a.serial_number)', '?1' ) ), 
                                    $queryBuilder->expr()->like( 'LOWER(a.location_text)', '?1' )
                            )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( "LOWER(CONCAT(CONCAT(b.name,' '),m.name))", '?1' )
                    );
            }
            $queryBuilder->setParameter( 1, strtolower($dstore['filter'][DStore::VALUE]) );
        }
        //$queryBuilder->andWhere( $queryBuilder->expr()->eq( 'bc.active', $queryBuilder->expr()->literal( true ) ) );
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
        $asset = $this->getDoctrine()
                        ->getRepository( 'AppBundle\Entity\Asset\Asset' )->find( $id );
        if( $asset !== null )
        {
            $model = $asset->getModel();
            $brand = $model->getBrand();
            $location = $asset->getLocation();
            if( $location === null )
            {
                $location = new Location();
                $locationId = $locationType = null;
            }
            else
            {
                $locationId = $location->getId();
                $locationTypeId = $location->getType();
                $locationType = $this->getDoctrine()
                                ->getRepository( 'AppBundle\Entity\Asset\LocationType' )->find( $locationTypeId );
                ;
            }
            $relationships = [
                'extends' => $model->getExtends( false ),
                'requires' => $model->getRequires( false ),
                'extended_by' => $model->getExtendedBy( false ),
                'required_by' => $model->getRequiredBy( false )
            ];
            $status = $asset->getStatus();
            $data = [
                'id' => $id,
                'model_text' => $brand->getName() . ' ' . $model->getName(),
                'model' => $model->getId(),
                'satisfies' => $model->getSatisfies(),
                'model_relationships' => $relationships,
                'serial_number' => $asset->getSerialNumber(),
                'location_text' => $asset->getLocationText(),
                'location' => [ 'id' => $locationId, 'entity' => $location->getEntity(), 'type' => $locationType],
                'status_text' => $status->getName(),
                'status' => $status->getId(),
                'barcodes' => $asset->getBarcodes(),
                'custom_attributes' => $asset->getCustomAttributes(),
                'comment' => $asset->getComment(),
                'purchased' => $asset->getPurchased()->format('Y-m-d'),
                'cost' => $asset->getCost(),
                'active' => $asset->isActive()
            ];

            $logUtil = $this->get( 'app.util.log' );
            $logUtil->getLog( 'AppBundle\Entity\Asset\AssetLog', $id );
            $data['history'] = $logUtil->translateIdsToText();
            $formUtil = $this->get( 'app.util.form' );
            $formUtil->saveDataTimestamp( 'asset' . $asset->getId(), $asset->getUpdated() );
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
        $data = $request->request->all();
        if( $id === "null" )
        {
            $asset = new Asset();
        }
        else
        {
            $asset = $em->getRepository( 'AppBundle\Entity\Asset\Asset' )->find( $id );
            $formUtil = $this->get( 'app.util.form' );
            if( $formUtil->checkDataTimestamp( 'asset' . $asset->getId(), $asset->getUpdated() ) === false )
            {
                throw new Exception( "data.outdated", 400 );
            }
        }
        if( $asset->getLocation() === null )
        {
            $asset->setLocation( new Location() );
        }
        $form = $this->createForm( AssetType::class, $asset, ['allow_extra_fields' => true] );
        try
        {
            $form->submit( $data );
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
    public function patchAssetAction( $id, Request $request )
    {
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'AppBundle\Entity\Asset\Asset' );
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
        $asset = $em->getRepository( 'AppBundle\Entity\Asset\Asset' )->find( $id );
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
