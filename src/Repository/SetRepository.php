<?php

Namespace App\Repository;

/**
 * Asset\Set\Repository
 */
class SetRepository extends \Doctrine\ORM\EntityRepository
{

    public function findBySatisfies($categoryIds)
    {
        if( !empty( $categoryIds ) )
        {

            $em = $this->getEntityManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['s','ss.id AS category_id'] )
                    ->from( 'App\Entity\Asset\Set', 's' )
                    ->join( 's.satisfies', 'ss' )
                    ->where( "ss.id IN (:category_ids)" )
                    ->setParameter( 'category_ids', $categoryIds);
            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

}
