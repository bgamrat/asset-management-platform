<?php

namespace AppBundle\Repository;

/**
 * LocationTypeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LocationTypeRepository extends \Doctrine\ORM\EntityRepository
{

    public function findAll()
    {
        return $this->findBy( [], ['name' => 'ASC'] );
    }

}