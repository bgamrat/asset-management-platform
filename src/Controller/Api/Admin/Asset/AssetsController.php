<?php

Namespace App\Controller\Api\Admin\Asset;

use App\Util\DStore;
use App\Util\Log;
use App\Util\Form as FormUtil;
use App\Entity\Asset\Asset;
use App\Entity\Asset\Location;
use App\Form\Admin\Asset\AssetType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\View\View as FOSRestView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AssetsController extends FOSRestController
{

    private $dstore;
    private $log;
    private $formUtil;

    public function __construct( DStore $dstore, Log $log, FormUtil $formUtil )
    {
        $this->dstore = $dstore;
        $this->log = $log;
        $this->formUtil = $formUtil;
    }

    /**
     * @View()
     */
    public function getAssetsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->dstore->gridParams( $request, 'id' );
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
            case 'status_text':
                $sortField = 's.name';
                break;
            default:
                $sortField = 'a.' . $dstore['sort-field'];
        }
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $columns = ['a.id', 'a.location_text', 'bc.barcode', 'bc.updatedAt AS barcode_updated',
            "CONCAT(CONCAT(b.name,' '),m.name) AS model_text", 'm.id AS model', 'a.serial_number',
            's.name AS status_text', 's.id AS status',
            'a.comment', 'a.active'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 'a.deletedAt AS deleted_at';
        }
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'App\Entity\Asset\Asset', 'a' )
                ->innerJoin( 'a.model', 'm' )
                ->innerJoin( 'm.brand', 'b' )
                ->leftJoin( 'a.barcodes', 'bc' ) // 'WITH', 'bc.active = true' )
                ->leftJoin( 'a.status', 's' )
                ->orderBy( $sortField, $dstore['sort-direction'] );
        $limit = 0;
        if( $dstore['limit'] !== null )
        {
            $limit = $dstore['limit'];
            $queryBuilder->setMaxResults( $limit );
        }
        $offset = 0;
        if( $dstore['offset'] !== null )
        {
            $offset = $dstore['offset'];
            $queryBuilder->setFirstResult( $offset );
        }
        if( $dstore['filter'] !== null )
        {
            switch( $dstore['filter'][DStore::OP] )
            {
                case DStore::LIKE:
                    $queryBuilder->where(
                            $queryBuilder->expr()->orX(
                                    $queryBuilder->expr()->orX(
                                            $queryBuilder->expr()->like( "LOWER(CONCAT(CONCAT(b.name,' '),m.name))", '?1' ), $queryBuilder->expr()->like( 'LOWER(a.serial_number)', '?1' ) ), $queryBuilder->expr()->orX(
                                            $queryBuilder->expr()->like( 'LOWER(a.location_text)', '?1' ), $queryBuilder->expr()->like( 'LOWER(bc.barcode)', '?1' )
                                    )
                            )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( "LOWER(CONCAT(CONCAT(b.name,' '),m.name))", '?1' )
                    );
            }
            $queryBuilder->setParameter( 1, strtolower( $dstore['filter'][DStore::VALUE] ) );
        }
        else
        {
            $queryBuilder->andWhere( $queryBuilder->expr()->eq( 'bc.active', $queryBuilder->expr()->literal( 't' ) ) );
        }
        $data = $queryBuilder->getQuery()->getResult();
        $count = $em->getRepository( 'App\Entity\Asset\Asset' )->count( [] );

        $view = FOSRestView::create();
        $view->setData( $data );
        $view->setHeader( 'Content-Range', 'items ' . $offset . '-' . ($offset + $limit) . '/' . $count );
        $handler = $this->get( 'fos_rest.view_handler' );
        return $handler->handle( $view );
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
                        ->getRepository( 'App\Entity\Asset\Asset' )->findConcise( $id );
        if( $asset !== null )
        {
            $logUtil = $this->log;
            $logUtil->getLog( 'App\Entity\Asset\AssetLog', $id );
            $history = $logUtil->translateIdsToText();
            $formUtil = $this->formUtil;
            $formUtil->saveDataTimestamp( 'asset' . $asset['id'], $asset['updated_at'] );
            $asset['history'] = $history;
            return $asset;
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
            $asset = $em->getRepository( 'App\Entity\Asset\Asset' )->find( $id );
            $formUtil = $this->formUtil;
            if( $formUtil->checkDataTimestamp( 'asset' . $asset->getId(), $asset->getUpdatedAt() ) === false )
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
                return $form->getErrors();
            }
        }
        catch( Exception $e )
        {
            $translator = $this->container->get( 'translator' )->getLocale();
            $response->setStatusCode( 400 );
            $response->setContent( json_encode(
                            ['message' => 'errors', 'errors' => $translator->translate( $e->getMessage() ), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTraceAsString()]
            ) );
        }
        return $response;
    }

    /**
     * @View(statusCode=204)
     */
    public function patchAssetAction( $id, Request $request )
    {
        $formProcessor = $this->formUtil;
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'App\Entity\Asset\Asset' );
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
        $asset = $em->getRepository( 'App\Entity\Asset\Asset' )->find( $id );
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
