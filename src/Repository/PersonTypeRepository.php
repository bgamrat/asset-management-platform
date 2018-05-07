<?php

Namespace App\Repository;

/**
 * IssueTypeApp\Repository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PersonTypeRepository extends \Doctrine\ORM\EntityRepository
{

    public function findAll()
    {
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT p FROM App\Entity\Common\PersonType p ORDER BY p.type ASC"
                        )
                        ->getResult();
    }

}