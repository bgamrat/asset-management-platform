<?php

/*
 * @TODO: clean this up
 */

Namespace App\Form\Admin\Client\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TrailerToIdTransformer implements DataTransformerInterface
{

    private $em;

    public function __construct( EntityManager $em )
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (trailer) to a string (id).
     *
     * @param  Issue|null $trailer
     * @return string
     */
    public function transform( $trailer )
    {

        if( null === $trailer )
        {
            return '';
        }
        if( is_array( $trailer ) && isset( $trailer['name'] ) )
        {
            return $trailer['name'];
        }
        return $trailer->getName();
    }

    /**
     * Transforms a string (id) to an object (trailer).
     *
     * @param  string $trailerId
     * @return Issue|null
     * @throws TransformationFailedException if object (trailer) is not found.
     */
    public function reverseTransform( $trailerName = null )
    {
        // no trailer id? It's optional, so that's ok
        if( !$trailerName )
        {
            return;
        }

        $trailer = $this->em
                ->getRepository( 'App\Entity\Asset\Trailer' )
                ->findOneBy( ['name' => $trailerName] )
        ;

        if( null === $trailer )
        {
            $trailer = $this->em
                    ->getRepository( 'App\Entity\Asset\Trailer' )
                    ->find( ['id' => $trailerName] )
            ;
            if( null === $trailer )
            {
                throw new TransformationFailedException( sprintf(
                        'An trailer with name "%s" does not exist!', $trailerName
                ) );
            }
        }
        return $trailer;
    }

}
