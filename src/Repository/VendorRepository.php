<?php

Namespace App\Repository;

use App\Entity\Asset\Vendor;

/**
 * VendorApp\Repository
 */
class VendorRepository extends \Doctrine\ORM\EntityRepository
{

    public function findByNameLike( $name )
    {
        $name = '%' . str_replace( '*', '%', strtolower( $name ) );
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT v FROM Entity\Asset\Vendor v WHERE LOWER(v.name) LIKE :name ORDER BY v.name ASC"
                        )
                        ->setParameter( 'name', $name )
                        ->getResult();
    }

    public function findByContacts( $contactIds )
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->select( ['v.id', 'v.name', 'c.id AS contact_id'] )
                ->from( 'App\Entity\Asset\Vendor', 'v' )
                ->leftJoin( 'v.contacts', 'c' );
        $queryBuilder->where( $queryBuilder->expr()->in( 'c.id', ':ids' ) )
                ->setParameter( 'ids', $contactIds );
        return $queryBuilder->getQuery()->getResult();
    }

}
