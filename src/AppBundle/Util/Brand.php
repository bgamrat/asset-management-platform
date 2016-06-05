<?php

namespace AppBundle\Util;

use AppBundle\Entity\Brand As BrandEntity;
use Doctrine\ORM\EntityManager;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Brand
{

    private $em;

    public function __construct( EntityManager $em )
    {
        $this->em = $em;
    }

    public function update( $entity, $data )
    {
        if( $entity === null )
        {
            throw new \Exception( 'error.cannot_be_null' );
        }
        $existingBrands = $entity->getBrands();
        $existing = [];
        if( !empty( $existingBrands ) )
        {
            foreach( $existingBrands as $b )
            {
                $existing[strtolower($b->getName())] = $b->toArray();
            }
        }
        foreach( $data as $brandData )
        {
            if( $brandData['name'] !== '' )
            {
                $key = array_search( $brandData['name'], array_keys( $existing ), false );
                if( $key !== false )
                {
                    $brand = $existingBrands[$key];
                    unset( $existingBrands[$key] );
                }
                else
                {
                    $brand = new BrandEntity();
                }
                $brand->setName( $brandData['name'] )
                        ->setComment( $brandData['comment'] )
                        ->setActive( $brandData['active'] );
                $this->em->persist($brand);
                if( $key === false )
                {
                    $entity->addBrand($brand);
                }
            }
        }
        if( !empty( $existingBrands ) )
        {
            foreach( $existingBrands as $leftOver )
            {
                $entity->removeBrand( $leftOver );
            }
        }
    }

}
