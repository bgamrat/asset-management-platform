<?php

Namespace App\Form\Admin\Asset\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TrailerRelationshipsToIdsTransformer implements DataTransformerInterface
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
    public function transform( $trailers )
    {
        if( empty($trailers) )
        {
            return null;
        }
        $ret = [];
        foreach ($trailers as $m) {
            $ret[] = $m->getId();
        }
        return $ret;
    }

    /**
     * Transforms a string (id) to an object (trailer).
     *
     * @param  string $trailerId
     * @return Issue|null
     * @throws TransformationFailedException if object (trailer) is not found.
     */
    public function reverseTransform( $trailerIds )
    {
        // no trailer id? It's optional, so that's ok
        if( empty( $trailerIds ) )
        {
            return [];
        }

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select( 'm' )
                ->from( 'App\Entity\Asset\Trailer', 'm' )
                ->where( $queryBuilder->expr()->in( 'm.id', ':trailerIds' ) )
                ->setParameter( 'trailerIds', $trailerIds )
                ->orderBy( 'm.name', 'ASC' );
        $trailers = $queryBuilder->getQuery()->getResult();

        if( null === $trailers )
        {
            throw new TransformationFailedException( sprintf(
                    'An trailer with id "%s" does not exist!', implode(',',$trailerIds)
            ) );
        }
        return $trailers;
    }

}
