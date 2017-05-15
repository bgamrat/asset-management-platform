<?php

namespace AppBundle\Repository;

/**
 * PersonRepository
 */
class PersonRepository extends \Doctrine\ORM\EntityRepository
{

    public function findByNameLike( $name )
    {
        $name = '%' . str_replace( '*', '%', strtolower($name) );
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT p FROM AppBundle\Entity\Common\Person p WHERE LOWER(CONCAT(p.firstname, ' ',COALESCE(CONCAT(p.middlename,' '),''), p.lastname)) LIKE :name ORDER BY p.lastname ASC"
                        )
                        ->setParameter( 'name', $name )
                        ->getResult();
    }

}
