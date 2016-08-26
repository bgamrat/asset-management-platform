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
        $queryBuilder = $em->createQueryBuilder()->select( ['a.model', 'a.serial_number', 'a.comment', 'a.active'] )
                ->from( 'AppBundle:Asset', 'a' )
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
        $query = $queryBuilder->getQuery();
        $assetCollection = $query->getResult();
        $data = [];
        foreach( $assetCollection as $a )
        {
            $item = [
                'model' => $a->getModel(),
                'serial_number' => $a->getSerialNumber(),
                'comment' => $a->getComment(),
                'active' => $a->isActive(),
            ];
            if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
            {
                $item['deleted_at'] = $a->getDeletedAt();
            }
            $data[] = $item;
        }
        return $data;
    }

    /**
     * @View()
     */
    public function getAssetAction( $name )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $repository = $this->getDoctrine()
                ->getRepository( 'AppBundle:Asset' );
        $asset = $repository->findOneBy( ['name' => $name] );
        if( $asset !== null )
        {
            $personModel = $this->get( 'app.model.person' );
            // TODO: Add full multi-contact support
            $contacts = $asset->getContacts();
            $data = [
                'name' => $asset->getName(),
                'brands' => $asset->getBrands(),
                'contacts' => [$personModel->get( $contacts[0] )],
                'active' => $asset->isActive()
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
    public function postAssetAction( $name, Request $request )
    {
        return $this->putAssetAction( $name, $request );
    }

    /**
     * @View()
     */
    public function putAssetAction( $name, Request $request )
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
            $asset = $em->getRepository( 'AppBundle:Asset' )->findOneBy( ['name' => $name] );

            if( $asset === null )
            {
                $asset = new Asset();
                $asset->setName( $data['name'] );
            }
            $contactUtil = $this->get( 'app.util.contact' );
            $contactUtil->update( $asset, $data['person'] );
            $brandUtil = $this->get( 'app.util.brand' );
            $brandUtil->update( $asset, $data['brands'] );
            $em->persist( $asset );
            $em->flush();

            $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
            $response->headers->set( 'Location', $this->generateUrl(
                            'app_admin_api_asset_get_asset', array('name' => $asset->getName()), true // absolute
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
    public function patchAssetAction( $name, Request $request )
    {
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'AppBundle:Asset' );
        $asset = $repository->findOneBy( ['name' => $name] );
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
    public function deleteAssetAction( $name )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->enable( 'softdeleteable' );
        $asset = $em->getRepository( 'AppBundle:Asset' )->findOneBy( ['name' => $name] );
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
