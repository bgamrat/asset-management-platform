<?php

namespace AppBundle\Form\Admin\Asset\DataTransformer;

use AppBundle\Entity\LocationType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class LocationTypeToIdTransformer implements DataTransformerInterface
{

    private $em;

    public function __construct( EntityManager $em )
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (LocationType) to a string (id).
     *
     * @param  Issue|null $LocationType
     * @return string
     */
    public function transform( $locationType )
    {
        if( empty( $locationType ) )
        {
            return '';
        }

        return $locationType->getId();
    }

    /**
     * Transforms a string (id) to an object (LocationType).
     *
     * @param  string $LocationTypeId
     * @return Issue|null
     * @throws TransformationFailedException if object (LocationType) is not found.
     */
    public function reverseTransform( $locationTypeId )
    {
        // no LocationType id? It's optional, so that's ok
        if( !$locationTypeId )
        {
            return;
        }

        $locationType = $this->em
                ->getRepository( 'AppBundle:LocationType' )
                ->find( $locationTypeId )
        ;

        if( null === $locationType )
        {
            throw new TransformationFailedException( sprintf(
                    'An LocationType with id "%s" does not exist!', $locationTypeId
            ) );
        }
        return $locationType;
    }

}
