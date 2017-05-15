<?php

namespace AppBundle\Repository;

/**
 * ClientRepository
 */
class ClientRepository extends \Doctrine\ORM\EntityRepository
{

    public function findByNameLike( $name )
    {
        $name = '%' . str_replace( '*', '%', strtolower( $name ) );
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT v FROM AppBundle\Entity\Asset\Client c WHERE LOWER(c.name) LIKE :name ORDER BY c.name ASC"
                        )
                        ->setParameter( 'name', $name )
                        ->getResult();
    }

    public function findByContacts( $contactIds )
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->select( ['c.id', 'c.name', 'cc.id AS contact_id'] )
                ->from( 'AppBundle\Entity\Client\Client', 'c' )
                ->leftJoin( 'c.contacts', 'cc' );
        $queryBuilder->where( $queryBuilder->expr()->in( 'cc.id', ':ids' ) )
                ->setParameter( 'ids', $contactIds );
        return $queryBuilder->getQuery()->getResult();
    }

}
