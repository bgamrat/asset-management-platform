<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Asset\Asset;

/**
 * AssetRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AssetRepository extends \Doctrine\ORM\EntityRepository
{

    public function findOneByBarcodeId( $barcodeId )
    {
        if( !empty( $barcodeId ) )
        {
            
            $em = $this->getEntityManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['a' ])
                    ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                    ->join( 'a.barcodes', 'b' )
                    ->where( "b.id = :barcode_id" )
                    ->setParameter( 'barcode_id', strtolower( $barcodeId ) );

            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }
    
    public function findByBarcode( $barcode )
    {
        if( !empty( $barcode ) )
        {          
            $em = $this->getEntityManager();
            $queryBuilder = $em->createQueryBuilder()->select( ['a' ])
                    ->from( 'AppBundle\Entity\Asset\Asset', 'a' )
                    ->join( 'a.barcodes', 'b' )
                    ->where( "LOWER(b.barcode) LIKE :barcode" )
                    ->setParameter( 'barcode', strtolower( $barcode ) );
            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }
}