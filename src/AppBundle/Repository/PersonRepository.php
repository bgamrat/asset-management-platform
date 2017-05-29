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
                ->join( 'c.type', 'e' )
                ->where( "LOWER(c.name) LIKE :name AND e.entity IN (:types)" )
                ->setParameter( 'name', $contactName )
                ->setParameter( 'types', $entityTypes );
        $fullContacts = $queryBuilder->getQuery()->getResult();
        if( !empty( $fullContacts ) )
        {
            $contacts = [];
            foreach( $fullContacts as $c )
            {
                $p = $c->getPerson();
                $p->setContactId( $c->getId() );
                $p->setContactName( $c->getName() );
                $p->setContactEntityType( $c->getType()->getEntity() );
                $p->setContactEntityId( $c->getEntity() );
                $contacts[$c->getHash()] = $p;
            }
            return $contacts;
        }
        return [];
    }

    public function findByEntityContactNameLike( $contactName, $entities )
    {
        $contactName = '%' . str_replace( '*', '%', strtolower( $contactName ) );

        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()->select( 'p.id' )
                ->where( self::CONCAT_NAME_LIKE )
                ->setParameter( 'name', $contactName );

        foreach( $entities as $e )
        {
            switch( $e )
            {
                case 'client':
                    $queryBuilder->from( 'AppBundle\Entity\Client\Client', 'c' )
                            ->addSelect( ['c.id AS client_id', 'c.name AS client_name', "'client' AS entity"] )
                            ->leftJoin( 'c.contacts', 'p' )
                            ->orWhere( "LOWER(c.name) LIKE :name" );
                    break;
            }
        }

        $entityContacts = $queryBuilder->getQuery()->getResult();

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
                $p->setContactId( null );
                $p->setContactName( $contactIds[$id][$entityStr . '_name'] . ' - ' . $p->getFullName() );
                $p->setContactEntityType( $entityStr );
                $p->setContactEntityId( $contactIds[$id][$entityStr . '_id'] );
                $contacts[] = $p;
            }
            return $contacts;
        }
        return [];
    }

}
