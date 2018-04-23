<?php

Namespace App\Repository;

/**
 * ClientApp\Repository
 */
class ClientRepository extends \Doctrine\ORM\EntityRepository
{

    public function findByNameLike( $name )
    {
        $name = '%' . str_replace( '*', '%', strtolower( $name ) );
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT c FROM App\Entity\Client\Client c WHERE LOWER(c.name) LIKE :name ORDER BY c.name ASC"
                        )
                        ->setParameter( 'name', $name )
                        ->getResult();
    }

}
