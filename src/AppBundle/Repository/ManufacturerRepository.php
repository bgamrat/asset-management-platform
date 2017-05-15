<?php

namespace AppBundle\Repository;

/**
 * ManufacturerRepository
 */
class ManufacturerRepository extends \Doctrine\ORM\EntityRepository
{

    public function findByNameLike( $name )
    {
        $name = '%' . str_replace( '*', '%', strtolower( $name ) );
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT m FROM AppBundle\Entity\Asset\Manufacturer m WHERE LOWER(m.name) LIKE :name ORDER BY m.name ASC"
                        )
                        ->setParameter( 'name', $name )
                        ->getResult();
    }

    public function findByContacts( $contactIds )
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->select( ['m.id', 'm.name', 'c.id AS contact_id'] )
                ->from( 'AppBundle\Entity\Asset\Manufacturer', 'm' )
                ->leftJoin( 'm.contacts', 'c' );
        $queryBuilder->where( $queryBuilder->expr()->in( 'c.id', ':ids' ) )
                ->setParameter( 'ids', $contactIds );
        return $queryBuilder->getQuery()->getResult();
    }
}
