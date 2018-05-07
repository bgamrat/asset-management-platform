<?php

Namespace App\Controller\Api\Admin\Common;

use App\Util\DStore;
use App\Util\Log;
use App\Util\Form as FormUtil;
use App\Entity\Common\Person;
use App\Entity\Common\Location;
use App\Form\Common\PersonType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PeopleController extends FOSRestController
{

    private $dstore;
    private $log;
    private $formUtil;

    public function __construct( DStore $dstore, Log $log, FormUtil $formUtil ) {
        $this->dstore = $dstore;
        $this->log = $log;
        $this->formUtil = $formUtil;
    }

    /**
     * @View()
     */
    public function getPeopleAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $staff = $request->headers->has( 'X-Staff' ) ? $request->headers->get( 'X-Staff' ) === '1' : false;

        $dstore = $this->dstore->gridParams( $request, 'id' );
        switch( $dstore['sort-field'] )
        {
            case 'name':
                $sortField = 'p.lastname';
                break;
            default:
                $sortField = 'p.' . $dstore['sort-field'];
        }
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $columns = ['p.id'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 'p.deletedAt AS deleted_at';
        }
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'App\Entity\Common\Person', 'p' )
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
                            $queryBuilder->expr()->like( 'LOWER(p.lastname)', '?1' )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( 'LOWER(p.lastname)', '?1' )
                    );
            }
            $queryBuilder->setParameter( 1, strtolower( $dstore['filter'][DStore::VALUE] ) );
        }
        if( $staff === true )
        {
            $queryBuilder->join( 'p.employment_statuses', 'pes' )
                    ->join( 'pes.employment_status', 'es' )
                    ->andWhere( 'es.active = TRUE' );
        }
        $ids = $queryBuilder->getQuery()->getResult();

        $data = [];
        foreach( $ids as $i => $row )
        {
            $person = $em->getRepository( 'App\Entity\Common\Person' )->find( $row['id'] );
            $p = ['id' => $row['id'],
                'name' => $person->getFullName(),
                'type_text' => $person->getType()->getType(),
                'emails' => $person->getEmails(),
                'addresses' => $person->getAddresses(),
                'phones' => $person->getPhones(),
                'active' => $person->isActive()];
            $data[] = $p;
        }
        return array_values( $data );
    }

    /**
     * @View()
     */
    public function getPersonAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $person = $this->getDoctrine()
                        ->getRepository( 'App\Entity\Common\Person' )->find( $id );
        if( $person !== null )
        {
            $formUtil = $this->formUtil;
            $formUtil->saveDataTimestamp( 'person' . $person->getId(), $person->getUpdatedAt() );

            $form = $this->createForm( PersonType::class, $person, ['allow_extra_fields' => true] );

            $logUtil = $this->log;
            $logUtil->getLog( 'App\Entity\Common\PersonLog', $id );
            $history = $logUtil->translateIdsToText();

            $person->setHistory( $history );
            $form->add( 'history', TextareaType::class, ['data' => $history] );
            return $form->getViewData();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

    /**
     * @View()
     */
    public function postPeopleAction( $id, Request $request )
    {
        return $this->putPersonAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putPeopleAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $data = $request->request->all();
        if( $id === "null" )
        {
            $person = new Person();
        }
        else
        {
            $person = $em->getRepository( 'App\Entity\Common\Person' )->find( $id );
            $formUtil = $this->formUtil;
            if( $formUtil->checkDataTimestamp( 'person' . $person->getId(), $person->getUpdatedAt() ) === false )
            {
                throw new Exception( "data.outdated", 400 );
            }
        }
        $form = $this->createForm( PersonType::class, $person, ['allow_extra_fields' => true] );
        try
        {
            $form->submit( $data );
            if( $form->isValid() )
            {
                $person = $form->getData();
                $em->persist( $person );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'get_person', array('id' => $person->getId()), true // absolute
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
    public function patchPeopleAction( $id, Request $request )
    {
        $formProcessor = $this->formUtil;
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'App\Entity\Common\Person' );
        $person = $repository->find( $id );
        if( $person !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $person->setActive( $value );
                        break;
                }

                $em->persist( $person );
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deletePeopleAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $person = $em->getRepository( 'App\Entity\Common\Person' )->find( $id );
        if( $person !== null )
        {
            $em->getFilters()->enable( 'softdeleteable' );
            $em->remove( $person );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }

}
