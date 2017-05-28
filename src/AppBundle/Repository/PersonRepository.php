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

    public function findByContactNameLike( $contactName )
    {
        $contactName = '%' . str_replace( '*', '%', strtolower( $contactName ) );

        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()->select( [ 'c.id AS contact_id', 'c.name AS name', 'p.id'] )
                ->from( 'AppBundle\Entity\Common\Contact', 'c' )
                ->join( 'c.person', 'p' )
                ->where( self::CONCAT_NAME_LIKE )
                ->orWhere( "LOWER(c.name) LIKE :name" )
                ->setParameter( 'name', $contactName );

        $contacts = $queryBuilder->getQuery()->getResult();

        if( !empty( $contacts ) )
        {
            $contactIds = [];
            foreach( $contacts as $cc )
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
                $c->setContactName( $contactIds[$id]['name'] );
                $c->setContactType( $contactIds[$id]['entity'] );
                $c->setContactId( $contactIds[$id]['entity_id'] );
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

            $contacts = $queryBuilder->getQuery()->getResult();
            foreach( $contacts as $c )
            {
                $id = $c->getId();
                $c->setContactName( $contactIds[$id]['client_name'] . ' - ' . $c->getFullName() );
                $c->setContactType( 'client' );
                $c->setContactId( $contactIds[$id]['client_id'] );
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
                $c->setContactName( $contactIds[$id]['client_name'] . ' - ' . $c->getFullName() );
                $c->setContactType( 'client' );
                $c->setContactId( $contactIds[$id] );
            }
            return $contacts;
        }
        return [];
    }

}
