<?php

namespace AppBundle\Util;

use AppBundle\Entity\Model As ModelEntity;
use Doctrine\ORM\EntityManager;

/**
 * Description of Asset
 *
 * @author bgamrat
 */
class Asset
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
        $existingModels = $entity->getModels();
        $existing = [];
        if( !empty( $existingModels ) )
        {
            foreach( $existingModels as $b )
            {
                $existing[strtolower($b->getName())] = $b->toArray();
            }
        }
        foreach( $data as $modelData )
        {
            if( $modelData['name'] !== '' )
            {
                $key = array_search( $modelData['name'], array_keys( $existing ), false );
                if( $key !== false )
                {
                    $model = $existingModels[$key];
                    unset( $existingModels[$key] );
                }
                else
                {
                    $model = new ModelEntity();
                }
                $model->setName( $modelData['name'] )
                        ->setComment( $modelData['comment'] )
                        ->setActive( $modelData['active'] );
                $this->em->persist($model);
                if( $key === false )
                {
                    $entity->addModel($model);
                }
            }
        }
        if( !empty( $existingModels ) )
        {
            foreach( $existingModels as $leftOver )
            {
                $entity->removeModel( $leftOver );
            }
        }
    }

}