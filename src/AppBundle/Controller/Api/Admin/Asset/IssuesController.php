<?php

namespace AppBundle\Controller\Api\Admin\Asset;

use AppBundle\Util\DStore;
use AppBundle\Entity\Asset\Issue;
use AppBundle\Entity\Asset\Location;
use AppBundle\Form\Admin\Asset\IssueType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class IssuesController extends FOSRestController
{

    /**
     * @View()
     */
    public function getIssuesAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'id' );
        switch( $dstore['sort-field'] )
        {
            case 'name':
                $sortField = 't.name';
                break;
            default:
                $sortField = 'i.' . $dstore['sort-field'];
        }
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $columns = ['i'];
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $columns[] = 'i.deletedAt AS deleted_at';
        }
        $queryBuilder = $em->createQueryBuilder()->select( $columns )
                ->from( 'AppBundle\Entity\Asset\Issue', 'i' )
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
                                            $queryBuilder->expr()->like( 'LOWER(t.name)', '?1' ), $queryBuilder->expr()->like( 'LOWER(t.serial_number)', '?1' ) ), $queryBuilder->expr()->like( 'LOWER(t.location_text)', '?1' )
                            )
                    );
                    break;
                case DStore::GT:
                    $queryBuilder->where(
                            $queryBuilder->expr()->gt( 'LOWER(m.name)', '?1' )
                    );
            }
            $queryBuilder->setParameter( 1, strtolower( $dstore['filter'][DStore::VALUE] ) );
        }
        $data = $queryBuilder->getQuery()->getResult();
        return array_values( $data );
    }

    /**
     * @View()
     */
    public function getIssueAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $issue = $this->getDoctrine()
                        ->getRepository( 'AppBundle\Entity\Asset\Issue' )->find( $id );
        if( $issue !== null )
        {
            $model = $issue->getModel();
            $brand = $model->getBrand();
            $location = $issue->getLocation();
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
                'extends' => $issue->getExtends( false ),
                'requires' => $issue->getRequires( false ),
                'extended_by' => $issue->getExtendedBy( false ),
                'required_by' => $issue->getRequiredBy( false )
            ];
            $status = $issue->getStatus();
            $data = [
                'id' => $id,
                'model_text' => $brand->getName() . ' ' . $model->getName(),
                'model' => $model->getId(),
                'issue_relationships' => $relationships,
                'serial_number' => $issue->getSerialNumber(),
                'location_text' => $issue->getLocationText(),
                'location' => [ 'id' => $locationId, 'entity' => $location->getEntity(), 'type' => $locationType],
                'status_text' => $status->getName(),
                'status' => $status->getId(),
                'name' => $issue->getName(),
                'description' => $issue->getDescription(),
                'purchased' => $issue->getPurchased()->format( 'Y-m-d' ),
                'cost' => $issue->getCost(),
                'active' => $issue->isActive()
            ];

            $logUtil = $this->get( 'app.util.log' );
            $logUtil->getLog( 'AppBundle\Entity\Asset\IssueLog', $id );
            $data['history'] = $logUtil->translateIdsToText();
            $formUtil = $this->get( 'app.util.form' );
            $formUtil->saveDataTimestamp( 'issue' . $issue->getId(), $issue->getUpdated() );
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
    public function postIssueAction( $id, Request $request )
    {
        return $this->putIssueAction( $id, $request );
    }

    /**
     * @View()
     */
    public function putIssueAction( $id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $data = $request->request->all();
        if( $id === "null" )
        {
            $issue = new Issue();
        }
        else
        {
            $issue = $em->getRepository( 'AppBundle\Entity\Asset\Issue' )->find( $id );
            $formUtil = $this->get( 'app.util.form' );
            if( $formUtil->checkDataTimestamp( 'issue' . $issue->getId(), $issue->getUpdated() ) === false )
            {
                throw new Exception( "data.outdated", 400 );
            }
        }
        if( $issue->getLocation() === null )
        {
            $issue->setLocation( new Location() );
        }
        $form = $this->createForm( IssueType::class, $issue, ['allow_extra_fields' => true] );
        try
        {
            $form->submit( $data );
            if( $form->isValid() )
            {
                $issue = $form->getData();
                $em->persist( $issue );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_issues_get_issue', array('id' => $issue->getId()), true // absolute
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
    public function patchIssueAction( $id, Request $request )
    {
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'AppBundle\Entity\Asset\Issue' );
        $issue = $repository->find( $id );
        if( $issue !== null )
        {
            if( isset( $data['field'] ) && is_bool( $formProcessor->strToBool( $data['value'] ) ) )
            {
                $value = $formProcessor->strToBool( $data['value'] );
                switch( $data['field'] )
                {
                    case 'active':
                        $issue->setActive( $value );
                        break;
                }

                $em->persist( $issue );
                $em->flush();
            }
        }
    }

    /**
     * @View(statusCode=204)
     */
    public function deleteIssueAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        if( $this->isGranted( 'ROLE_SUPER_ADMIN' ) )
        {
            $em->getFilters()->disable( 'softdeleteable' );
        }
        $issue = $em->getRepository( 'AppBundle\Entity\Asset\Issue' )->find( $id );
        if( $issue !== null )
        {
            $em->getFilters()->enable( 'softdeleteable' );
            $em->remove( $issue );
            $em->flush();
        }
        else
        {
            throw $this->createNotFoundException( 'Not found!' );
        }
    }
}
