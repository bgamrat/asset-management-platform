<?php

namespace AppBundle\Repository;

use \AppBundle\Entity\Common\Contact;

use \Doctrine\ORM\Mapping\ClassMetadata;

/**
 * PersonRepository
 */
class PersonRepository extends \Doctrine\ORM\EntityRepository
{

    const CONCAT_NAME = "CONCAT(p.firstname, ' ',COALESCE(CONCAT(p.middlename,' '),''), p.lastname) AS name";
    const CONCAT_NAME_LIKE = "LOWER(CONCAT(p.firstname, ' ',COALESCE(CONCAT(p.middlename,' '),''), p.lastname)) LIKE :name OR LOWER(p.title) LIKE :name";

    private $contactTypes = [];

    public function __construct( $em, ClassMetadata $class )
    {
        parent::__construct( $em, $class );
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select( 'ct' )
                ->from( 'AppBundle\Entity\Common\ContactType', 'ct' );
        $contactTypeData = $queryBuilder->getQuery()->getResult();
        foreach( $contactTypeData as $ct )
        {
            $this->contactTypes[$ct->getEntity()] = $ct;
        }
    }

    public function findByNameLike( $name )
    {
        $name = '%' . str_replace( '*', '%', strtolower( $name ) );
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT p FROM AppBundle\Entity\Common\Person p WHERE "
                                . self::CONCAT_NAME_LIKE
                                . "ORDER BY p.lastname ASC"
                        )
                        ->setParameter( 'name', $name )
                        ->getResult();
    }

    public function findByContactNameLike( $contactName, $entityTypes )
    {
        $contactName = '%' . str_replace( '*', '%', strtolower( $contactName ) );

        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()->select( [ 'c'] )
                ->from( 'AppBundle\Entity\Common\Contact', 'c' )
                ->join( 'c.type', 'e' )
                ->where( "LOWER(c.name) LIKE :name AND e.entity IN (:types)" )
                ->setParameter( 'name', $contactName )
                ->setParameter( 'types', $entityTypes );
        return $queryBuilder->getQuery()->getResult();
    }

    public function findByEntityContactNameLike( $contactName, $entities )
    {
        $contactName = '%' . str_replace( '*', '%', strtolower( $contactName ) );

        $em = $this->getEntityManager();

        $entityContacts = [];
        foreach( $entities as $e )
        {
            $queryBuilder = $em->createQueryBuilder()->select( ['p.id'] )
                    ->where( self::CONCAT_NAME_LIKE )
                    ->setParameter( 'name', $contactName );
            switch( $e )
            {
                case 'client':
                    $queryBuilder->from( 'AppBundle\Entity\Client\Client', 'c' )
                            ->addSelect( ['c.id AS client_id', 'c.name AS client_name', "'client' AS entity"] )
                            ->leftJoin( 'c.contacts', 'p' )
                            ->orWhere( "LOWER(c.name) LIKE :name" );
                    break;
                case 'venue':
                    $queryBuilder->from( 'AppBundle\Entity\Venue\Venue', 'v' )
                            ->addSelect( ['v.id AS venue_id', 'v.name AS venue_name', "'venue' AS entity"] )
                            ->leftJoin( 'v.contacts', 'p' )
                            ->orWhere( "LOWER(v.name) LIKE :name" );
                    break;
            }
            $entityContacts = array_merge( $entityContacts, $queryBuilder->getQuery()->getResult() );
        }

        if( !empty( $entityContacts ) )
        {
            $contactIds = [];
            foreach( $entityContacts as $ec )
            {
                $contactIds[$ec['id']] = $ec;
            }

            $queryBuilder = $em->createQueryBuilder();
            $queryBuilder->select( 'p' )
                    ->from( 'AppBundle\Entity\Common\Person', 'p' )
                    ->where( $queryBuilder->expr()->in( 'p.id', array_keys( $contactIds ) ) );
            $people = $queryBuilder->getQuery()->getResult();

            $contacts = [];
            foreach( $people as $p )
            {
                $id = $p->getId();
                $entityStr = $contactIds[$id]['entity'];
                // This is a new contact created from the client list
                $contact = new Contact;
                $contact->setId( null );
                $contact->setPerson($p);
                $contact->setName( $contactIds[$id][$entityStr . '_name'] . ' - ' . $p->getFullName() );
                $contact->setType( $this->contactTypes[$entityStr] );
                $contact->setEntity( $contactIds[$id][$entityStr . '_id'] );
                $contacts[] = $contact;
            }
            return $contacts;
        }
        return [];
    }

}
