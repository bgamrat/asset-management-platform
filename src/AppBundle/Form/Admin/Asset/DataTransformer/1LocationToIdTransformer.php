<?php

namespace AppBundle\Form\Admin\Asset\DataTransformer;

use AppBundle\Entity\Location;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class LocationToIdTransformer implements DataTransformerInterface
{

    private $em;

    public function __construct( EntityManager $em )
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (location) to a string (id).
     *
     * @param  Issue|null $location
     * @return string
     */
    public function transform( $locationData )
    {

        if( empty( $locationData ) )
        {
            return null;
        }

        $location = new Location();
        if( !empty( $locationData['id'] ) )
        {
            $location->setId( $locationData['id'] );
        }
        $location->setType( $locationData['type'] );
        $location->setEntity( $locationData['entity'] );
        $location->setActive( $locationData['active'] );
        $location->setAssets( $locationData['assets'] );

        return $locationData;
    }

    /**
     * Transforms a string (id) to an object (location).
     *
     * @param  string $locationId
     * @return Issue|null
     * @throws TransformationFailedException if object (location) is not found.
     */
    public function reverseTransform( $location )
    {
        // no location id? It's optional, so that's ok
        if( $location->getType() === null )
        {
            return null;
        }
 
        return $location->toArray();
    }

}
