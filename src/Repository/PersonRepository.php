<?php

Namespace App\Repository;

use \Entity\Common\Contact;

use \Doctrine\ORM\Mapping\ClassMetadata;

/**
 * PersonApp\Repository
 */
class PersonRepository extends \Doctrine\ORM\EntityRepository
{

    const CONCAT_NAME = "CONCAT(p.firstname, ' ',COALESCE(CONCAT(p.middlename,' '),''), p.lastname) LIKE :name";
    const CONCAT_NAME_LIKE = "LOWER(CONCAT(p.firstname, ' ',COALESCE(CONCAT(p.middlename,' '),''), p.lastname)) LIKE :name OR LOWER(p.title) LIKE :name";

    private $contactTypes = [];

    public function __construct( $em, ClassMetadata $class )
    {
        parent::__construct( $em, $class );
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select( 'ct' )
                ->from( 'App\Entity\Common\ContactType', 'ct' );
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
                                "SELECT p FROM Entity\Common\Person p WHERE "
                                . self::CONCAT_NAME_LIKE
                                . " ORDER BY p.lastname ASC"
                        )
                        ->setParameter( 'name', $name )
                        ->getResult();
    }

    public function findByContactNameLike( $contactName, $entityTypes )
    {
        $contactName = '%' . str_replace( '*', '%', strtolower( $contactName ) );

        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()->select( [ 'c'] )
                ->from( 'App\Entity\Common\Contact', 'c' )
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
                    $queryBuilder->from( 'App\Entity\Client\Client', 'c' )
                            ->addSelect( ['c.id AS client_id', 'c.name AS client_name', "'client' AS entity"] )
                            ->leftJoin( 'c.contacts', 'p' )
                            ->orWhere( "LOWER(c.name) LIKE :name" );
                    break;
                case 'manufacturer':
                    $queryBuilder->from( 'App\Entity\Asset\Manufacturer', 'm' )
                            ->addSelect( ['m.id AS manufacturer_id', 'm.name AS manufacturer_name', "'manufacturer' AS entity"] )
                            ->leftJoin( 'm.contacts', 'p' )
                            ->orWhere( "LOWER(m.name) LIKE :name" );
                    break;
                case 'venue':
                    $queryBuilder->from( 'App\Entity\Venue\Venue', 'v' )
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
                if (!empty($ec['id'])) {
                    $contactIds[$ec['id']] = $ec;
                }
            }

            $queryBuilder = $em->createQueryBuilder();
            $queryBuilder->select( 'p' )
                    ->from( 'App\Entity\Common\Person', 'p' )
                    ->where( $queryBuilder->expr()->in( 'p.id', '?1'));
            $queryBuilder->setParameter(1,array_keys( $contactIds ) ) ;
            $people = $queryBuilder->getQuery()->getResult();

            $contacts = [];
            foreach( $people as $p )
            {
                $id = $p->getId();
                $addresses = $p->getAddresses();
                $entityStr = $contactIds[$id]['entity'];
                foreach ($addresses as $a) {
                    // This is a new contact
                    $contact = new Contact;
                    $contact->setId( null );
                    $contact->setPerson($p);
                    $contact->setName( $contactIds[$id][$entityStr . '_name'] . ' - ' . $p->getFullName() );
                    $contact->setType( $this->contactTypes[$entityStr] );
                    $contact->setAddress($a);
                    $contact->setEntity( $contactIds[$id][$entityStr . '_id'] );
                    $contacts[$contact->getHash()] = $contact;
                }
            }
            return array_values($contacts);
        }
        return [];
    }

}
