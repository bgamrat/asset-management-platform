<?php

Namespace App\Repository;

/**
 * ManufacturerApp\Repository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends \Doctrine\ORM\EntityRepository
{

    public function findAll()
    {
        $data = $this->getEntityManager()
                ->createQuery(
                        "SELECT c, p FROM Entity\Asset\Category c LEFT JOIN c.parent p ORDER BY c.position ASC, p.name"
                )
                ->getResult();
        // This moves the top category to the beginning of the array
        foreach( $data as $i => $d )
        {
            if( $d->getParent() === null )
            {
                $top = array_splice( $data, $i, 1 );
                array_unshift( $data, $top[0] );
                break;
            }
        }
        return $data;
    }

    public function findChildren()
    {
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT c, p FROM Entity\Asset\Category c LEFT JOIN c.parent p WHERE c.name != 'top' ORDER BY c.position ASC"
                        )
                        ->getResult();
    }

}
