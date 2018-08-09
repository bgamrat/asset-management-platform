<?php

Namespace App\Repository;

use App\Entity\Asset\Asset;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * AssetApp\Repository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AssetRepository extends ServiceEntityRepository
{

    public function __construct( RegistryInterface $registry )
    {
        parent::__construct( $registry, Asset::class );
    }

    public function findConcise( $assetId )
    {
        // Raw SQL is used for performance
        if( !empty( $assetId ) )
        {
            $em = $this->getEntityManager();
            $sql = <<< SQL
SELECT a.id,
       a.active,
       m.id AS model_id,
       m.name AS model_name,
       Concat_ws(' ', b.NAME, m.NAME)  AS brand_model_name,
       m.name AS model_text,
       a.status_id,
       st.NAME                         AS status_name,
       a.purchased,
       --Extract(epoch FROM a.purchased) AS purchased,
       a.cost,
       a.value,
       a.owner_id,
       a.location_id,
       l.type                          AS location_type,
       a.serial_number,
       a.custom_attributes,
       a.comment,
       a.updated_at
FROM   asset a
       JOIN model m
         ON ( a.model_id = m.id )
       JOIN brand b
         ON ( m.brand_id = b.id )
       JOIN asset_status st
         ON ( a.status_id = st.id )
       JOIN location l
         ON ( a.location_id = l.id )
WHERE  a.id = ?
SQL;
            $stmt = $em->getConnection()->prepare( $sql );
            $stmt->bindValue( 1, $assetId );
            $stmt->execute();
            $data = $stmt->fetchAll();
            if( !empty( $data ) )
            {
                $data = $data[0];
                $sql = 'SELECT * FROM barcode b WHERE b.asset_id = ?';
                $stmt = $em->getConnection()->prepare( $sql );
                $stmt->bindValue( 1, $assetId );
                $stmt->execute();
                $data['barcodes'] = $stmt->fetchAll();
                $data['model'] = $em->getRepository( 'App\Entity\Asset\Model' )->find( $data['model_id'] );
                $data['owner'] = empty( $data['owner_id'] ) ? null :
                        $data['owner'] = $em->getRepository( 'App\Entity\Asset\Vendor' )->find( $data['owner_id'] );
            }
        }
        else
        {
            $data = null;
        }
        return $data;
    }

    public function findOneByBarcodeId( $barcodeId )
    {
        if( !empty( $barcodeId ) )
        {

            $em = $this->getEntityManager();

            $queryBuilder = $em->createQueryBuilder()->select( ['a'] )
                    ->from( 'App\Entity\Asset\Asset', 'a' )
                    ->join( 'a.barcodes', 'b' )
                    ->where( "b.id = :barcode_id" )
                    ->setMaxResults( 1 )
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
            $queryBuilder = $em->createQueryBuilder()->select( ['a'] )
                    ->from( 'App\Entity\Asset\Asset', 'a' )
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

    public function findByLocation( $locationType, $locationId )
    {
        if( !empty( $locationType ) )
        {
            $em = $this->getEntityManager();
            $queryBuilder = $em->createQueryBuilder()->select( ['a', 'm'] )
                    ->from( 'App\Entity\Asset\Asset', 'a' )
                    ->join( 'a.location', 'l' )
                    ->join( 'a.model', 'm' )
                    ->where( 'l.type = :location_type' )
                    ->andWhere( 'l.entity = :location_id' )
                    ->orderBy( 'm.name' )
                    ->addOrderBy( 'm.id' )
                    ->setParameters( ['location_type' => $locationType, 'location_id' => $locationId] );
            $data = $queryBuilder->getQuery()->getResult();
        }
        else
        {
            $data = null;
        }
        return $data;
    }

    // Supports search drop down
    public function findByNameLike( $barcode )
    {
        $barcode = '%' . str_replace( '*', '%', strtolower( $barcode ) );
        return $this->findByBarcode( $barcode );
    }

}
