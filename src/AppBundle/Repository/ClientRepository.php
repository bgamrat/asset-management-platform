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

}
