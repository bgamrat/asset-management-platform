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

    public function findByClientContactNameLike( $contactName )
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()->select( [ 'c.name AS client_name', 'p.id'] )
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
                $contactIds[$cc['client_name']] = $cc['id'];
            }

            $queryBuilder = $em->createQueryBuilder();
            $queryBuilder->select( 'p' )
                    ->from( 'AppBundle\Entity\Common\Person', 'p' )
                    ->where( $queryBuilder->expr()->in( 'p.id', $contactIds ) );

            $contacts = $queryBuilder->getQuery()->getResult();
            $clientNamesByContactIds = array_flip( $contactIds );
            foreach( $contacts as $c )
            {
                $c->setContactName( $clientNamesByContactIds[$c->getId()] . ' - ' . $c->getFullName() );
            }
            return $contacts;
        }
        return [];
    }

}
