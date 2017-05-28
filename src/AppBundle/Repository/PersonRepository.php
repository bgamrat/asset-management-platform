<?php

namespace AppBundle\Repository;

/**
 * PersonRepository
 */
class PersonRepository extends \Doctrine\ORM\EntityRepository
{

    const CONCAT_NAME = "CONCAT(p.firstname, ' ',COALESCE(CONCAT(p.middlename,' '),''), p.lastname) AS name";
    const CONCAT_NAME_LIKE = "LOWER(CONCAT(p.firstname, ' ',COALESCE(CONCAT(p.middlename,' '),''), p.lastname)) LIKE :name OR LOWER(p.title) LIKE :name";

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
                ->join( 'c.type', 'e')
                ->where( "LOWER(c.name) LIKE :name AND e.entity IN (:types)" )
                ->setParameter( 'name', $contactName )
                ->setParameter( 'types', $entityTypes);
        $fullContacts = $queryBuilder->getQuery()->getResult();
        if( !empty( $fullContacts ) )
        {
            $contacts = [];
            foreach( $fullContacts as $c )
            {
                $p = $c->getPerson();
                $p->setContactId( $c->getId() );
                $p->setContactName( $c->getName() );
                $p->setContactEntityType( $c->getType()->getEntity());
                $p->setContactEntityId( $c->getEntity() );
                $contacts[$c->getHash()] = $p;
            }
            return $contacts;
        }
        return [];
    }

    public function findByClientContactNameLike( $contactName )
    {
        $contactName = '%' . str_replace( '*', '%', strtolower( $contactName ) );

        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()->select( [ 'c.id AS client_id', 'c.name AS client_name', 'p.id'] )
                ->from( 'AppBundle\Entity\Client\Client', 'c' )
                ->join( 'c.contacts', 'p' )
                ->where( self::CONCAT_NAME_LIKE )
                ->orWhere( "LOWER(c.name) LIKE :name" )
                ->setParameter( 'name', $contactName );

        $clientContacts = $queryBuilder->getQuery()->getResult();

        if( !empty( $clientContacts ) )
        {
            $contactIds = [];
            foreach( $clientContacts as $cc )
            {
                $contactIds[$cc['id']] = $cc;
            }

            $queryBuilder = $em->createQueryBuilder();
            $queryBuilder->select( 'p' )
                    ->from( 'AppBundle\Entity\Common\Person', 'p' )
                    ->where( $queryBuilder->expr()->in( 'p.id', array_keys( $contactIds ) ) );

            $people = $queryBuilder->getQuery()->getResult();
            $contacts = [];
            foreach( $people as $p)
            {
                $id = $p->getId();
                // This is a new contact created from the client list
                $p->setContactId( null );
                $p->setContactName( $contactIds[$id]['client_name'] . ' - ' . $p->getFullName() );
                $p->setContactEntityType( 'client' );
                $p->setContactEntityId( $contactIds[$id]['client_id'] );
                $contacts[] = $p;
            }
            return $contacts;
        }
        return [];
    }

    public function findByManufacturerContactNameLike( $contactName )
    {
        $contactName = '%' . str_replace( '*', '%', strtolower( $contactName ) );
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()->select( [ 'c.id AS client_id', 'c.name AS client_name', 'p.id'] )
                ->from( 'AppBundle\Entity\Client\Client', 'c' )
                ->join( 'c.contacts', 'p' )
                ->where( self::CONCAT_NAME_LIKE )
                ->orWhere( "LOWER(c.name) LIKE :name" )
                ->setParameter( 'name', $contactName );

        $clientContacts = $queryBuilder->getQuery()->getResult();

        if( !empty( $clientContacts ) )
        {
            $contactIds = [];
            foreach( $clientContacts as $cc )
            {
                $contactIds[$cc['id']] = $cc;
            }

            $queryBuilder = $em->createQueryBuilder();
            $queryBuilder->select( 'p' )
                    ->from( 'AppBundle\Entity\Common\Person', 'p' )
                    ->where( $queryBuilder->expr()->in( 'p.id', array_keys( $contactIds ) ) );

            $contacts = $queryBuilder->getQuery()->getResult();
            foreach( $contacts as $c )
            {
                $id = $c->getId();
                // This is a new contact created from the manufacturer list
                $c->setContactId( null );
                $c->setContactName( $contactIds[$id]['manufacturer_name'] . ' - ' . $c->getFullName() );
                $c->setContactEntityType( 'manufacturer' );
                $c->setContactEntityId( $contactIds[$id]['manufacturer_id'] );
            }
            return $contacts;
            return $contacts;
        }
        return [];
    }

}
