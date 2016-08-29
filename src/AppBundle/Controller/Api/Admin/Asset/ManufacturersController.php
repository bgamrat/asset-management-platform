<?php

namespace AppBundle\Controller\Api\Admin\Asset;

use AppBundle\Util\DStore;
use AppBundle\Entity\Manufacturer;
use AppBundle\Form\Admin\Asset\BrandsType;
use AppBundle\Form\Admin\Asset\ManufacturerType;
use AppBundle\Form\Admin\Asset\ModelsType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Util\Model As ModelUtil;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

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
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceHandler( function ($object)
        {
            return $object->getName();
        } );

        $serializer = new Serializer( array($normalizer), array($encoder) );

        return $serializer->normalize( $data );
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
        $response = new Response();
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $form = $this->createForm( ManufacturerType::class, null, [] );
        try
        {
            $formProcessor->validateFormData( $form, $data );
            $em = $this->getDoctrine()->getManager();
            $manufacturer = $em->getRepository( 'AppBundle:Manufacturer' )->findOneBy( ['name' => $name] );

            if( $manufacturer === null )
            {
                $manufacturer = new Manufacturer();
                $manufacturer->setName( $data['name'] );
            }
            $contactUtil = $this->get( 'app.util.contact' );
            $contactUtil->update( $manufacturer, $data['person'] );
            $brandUtil = $this->get( 'app.util.brand' );
            $brandUtil->update( $manufacturer, $data['brands'] );
            $em->persist( $manufacturer );
            $em->flush();

            $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
            $response->headers->set( 'Location', $this->generateUrl(
                            'app_admin_api_manufacturer_get_manufacturer', array('name' => $manufacturer->getName()), true // absolute
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
     * @Route("/api/manufacturers/{mname}/brand/{bname}/models")
     * @Method("GET")
     * @View()
     */
    public function getManufacturerBrandModelsAction( $mname, $bname, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->createQueryBuilder()->select( 'm, b, d' )
                ->from( 'AppBundle:Manufacturer', 'm' )
                ->innerJoin( 'm.brands', 'b' )
                ->innerJoin( 'b.models', 'd' )
                ->where( "m.name = :mname AND b.name = :bname" )
                ->setParameters( ['mname' => $mname, 'bname' => $bname] );
        $manufacturer = $queryBuilder->getQuery()->getResult();

        if( $manufacturer !== null )
        {
            $data = [];
            if( count( $manufacturer ) > 0 )
            {
                $brands = $manufacturer[0]->getBrands();
                if( count( $brands ) > 0 )
                {
                    $data['models'] = $brands[0]->getModels();
                }
                $encoder = new JsonEncoder();
                $normalizer = new ObjectNormalizer();

                $normalizer->setCircularReferenceHandler( function ($object)
                {
                    return $object->getName();
                } );

                $serializer = new Serializer( array($normalizer), array($encoder) );

                return $serializer->normalize( $data );
            }
            return $data;
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @Route("/api/manufacturers/{mname}/brand/{bname}/models")
     * @Method("POST")
     * @View()
     */
    public function postManufacturerBrandModelsAction( $mname, $bname, Request $request )
    {
        return $this->putManufacturerBrandModelsAction( $mname, $bname, $request );
    }

    /**
     * @Route("/api/manufacturers/{mname}/brand/{bname}/models")
     * @Method("PUT")
     * @View()
     */
    public function putManufacturerBrandModelsAction( $mname, $bname, Request $request )
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
            $query = $em->createQuery( 'SELECT m, b FROM AppBundle\Entity\Manufacturer m JOIN m.brands b WHERE m.name = :mname AND b.name = :bname' );
            $query->setParameters( ['mname' => $mname, 'bname' => $bname] );
            $manufacturer = $query->getResult();
            $brand = $manufacturer[0]->getBrands()[0];
            if( $brand !== null )
            {
                $modelUtil = $this->get( 'app.util.model' );
                $modelUtil->update( $brand, $data['models'] );
                $em->persist( $brand );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_manufacturers_get_manufacturer_brand_models', array('mname' => $mname, 'bname' => $bname), true // absolute
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
