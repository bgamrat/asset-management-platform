<?php

namespace AppBundle\Controller\Api\Admin\Common;

use AppBundle\Util\DStore;
use AppBundle\Entity\Person\Person;
use AppBundle\Entity\Person\Location;
use AppBundle\Form\Common\PersonType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class PeopleController extends FOSRestController
{

    /**
     * @View()
     */
    public function getPeopleAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $dstore = $this->get( 'app.util.dstore' )->gridParams( $request, 'id' );
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
                ->from( 'AppBundle\Entity\Common\Person', 'p' )
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
            $queryBuilder->setParameter( 1, strtolower($dstore['filter'][DStore::VALUE]) );
        }

        $ids = $queryBuilder->getQuery()->getResult();
        
        $data = [];
        foreach ($ids as $i => $row) {
            $person = $em->getRepository( 'AppBundle\Entity\Common\Person' )->find( $row['id'] );
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
                        ->getRepository( 'AppBundle\Entity\Common\Person' )->find( $id );
        if( $person !== null )
        {
            $data = [
                'id' => $person->getId(),
                'firstname' => $person->getFirstname(),
                'middlename' => $person->getMiddlename(),
                'lastname' => $person->getLastname(),
                'emails' => $person->getEmails(),
                'addresses' => $person->getAddresses(),
                'phones' => $person->getPhones(),
                'active' => $person->isActive()
            ];

            $logUtil = $this->get( 'app.util.log' );
            $logUtil->getLog( 'AppBundle\Entity\Common\PersonLog', $id );
            $data['history'] = $logUtil->translateIdsToText();
            $formUtil = $this->get( 'app.util.form' );
            $formUtil->saveDataTimestamp( 'person' . $person->getId(), $person->getUpdated() );
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
            $person = $em->getRepository( 'AppBundle\Entity\Common\Person' )->find( $id );
            $formUtil = $this->get( 'app.util.form' );
            if( $formUtil->checkDataTimestamp( 'person' . $person->getId(), $person->getUpdated() ) === false )
            {
                throw new Exception( "data.outdated", 400 );
            }
        }
        $form = $this->createForm( PersonType::class, $person, ['allow_extra_fields' => true] );
        try
        {
            $form->submit($data);
            if( $form->isValid() )
            {
                $person = $form->getData();
                $em->persist( $person );
                $em->flush();
                $response->setStatusCode( $request->getMethod() === 'POST' ? 201 : 204  );
                $response->headers->set( 'Location', $this->generateUrl(
                                'app_admin_api_people_get_person', array('id' => $person->getId()), true // absolute
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
        $formProcessor = $this->get( 'app.util.form' );
        $data = $formProcessor->getJsonData( $request );
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository( 'AppBundle\Entity\Common\Person' );
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
        $person = $em->getRepository( 'AppBundle\Entity\Common\Person' )->find( $id );
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
