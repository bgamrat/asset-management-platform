<?php

Namespace App\Repository;

/**
 * ManufacturerApp\Repository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ModelRepository extends \Doctrine\ORM\EntityRepository
{
    public function findOrderedByBrandModelName( $name )
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT m FROM App\:Model m '
                    . 'JOIN brand_model AS bm ON m.id = bm.model_id'
                    . 'JOIN brand AS b ON bm.brand_id = b.id'
                    . 'WHERE CONCAT_WS(\' \',b.name,m.name) LIKE ?1'
                    . 'ORDER BY b.name,m.name ASC'
            )
            ->getResult();
    }
}
