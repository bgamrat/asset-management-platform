<?php

namespace AppBundle\Controller\Api\Admin\Asset;

use AppBundle\Entity\Vendor;
use AppBundle\Util\DStore;
use AppBundle\Form\Admin\Asset\VendorType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class VendorsController extends FOSRestController
{

   /**
     * @View()
     */
    public function getVendorsAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'name' );

        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $queryBuilder = $em->createQueryBuilder()->select( ['m'] )
                ->from( 'AppBundle:Vendor', 'm' )
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
        $vendorCollection = $query->getResult();
        $data = [];
        foreach( $vendorCollection as $u )
        {
            $item = [
                'name' => $u->getName(),
                'active' => $u->isActive(),
            ];
            if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
            {
                $item['deleted_at'] = $u->getDeletedAt();
            }
            $data[] = $item;
        }
        return $data;
    }

    /**
     * @View()
     */
    public function getVendorAction( $name )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Vendor');
        $vendor = $repository->findOneBy( ['name' => $name] );
        if( $vendor !== null )
        {
            $data = [
                'name' => $vendor->getName(),
                'active' => $vendor->isActive()
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
    public function postVendorAction( $name, Request $request )
    {
        return $this->putVendorAction( $name, $request );
    }

    /**
     * @View()
     */
    public function putVendorAction( $name, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $response = new Response();
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $form = $this->createForm( VendorType::class, null, [] );
        try
        {
            $formProcessor->validateFormData( $form, $data );
            $em = $this->getDoctrine()->getManager();
            $vendor = $em->getRepository('AppBundle:Vendor')->findOneBy( ['name' => $name] );
            if( $vendor === null )
            {
                $vendor = new Vendor();
                $vendor->setName( $data['name'] );
            }
            $em->persist($vendor);
            $em->flush();

            $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
            $response->headers->set( 'Location', $this->generateUrl(
                            'app_admin_api_vendor_get_vendor', array('name' => $vendor->getName()), true // absolute
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
    public function patchVendorAction( $name, Request $request )
    {
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
                $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Vendor');
        $vendor = $repository->findOneBy( ['name' => $name] );
        if( $vendor !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $vendor->setActive( $value );
                        break;
                }

                $em->persist($vendor);
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteVendorAction( $name )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->enable( 'softdeleteable' );
        $vendor = $em->getRepository( 'AppBundle:Vendor' )->findOneBy( ['name' => $name] );
        if( $vendor !== null )
        {
            $em->remove( $vendor );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

}
