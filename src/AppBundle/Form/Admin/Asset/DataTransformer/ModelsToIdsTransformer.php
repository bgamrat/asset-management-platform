<?php

namespace AppBundle\Form\Admin\Asset\DataTransformer;

use AppBundle\Entity\Model;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ModelsToIdsTransformer implements DataTransformerInterface
{

    private $em;

    public function __construct( EntityManager $em )
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (model) to a string (id).
     *
     * @param  Issue|null $model
     * @return string
     */
    public function transform( $models )
    {

        if( empty( $models ) )
        {
            return [];
        }
        $ids = [];
        foreach( $models as $m )
        {
            $ids[] = $m->getId();
        }
        return $ids;
    }

    /**
     * Transforms a string (id) to an object (model).
     *
     * @param  string $modelId
     * @return Issue|null
     * @throws TransformationFailedException if object (model) is not found.
     */
    public function reverseTransform( $modelIds )
    {
        // no model id? It's optional, so that's ok
        if( !$modelIds )
        {
            return;
        }

        $query = $repository->createQueryBuilder( 'm' )
                ->where( $qb->expr()->in( 'm.id', $modelIds ) )
                ->orderBy( 'm.name', 'ASC' );

        $models = $query->getQuery()->getResult();
        if( null === $models )
        {
            throw new TransformationFailedException( sprintf(
                    'An model with id "%s" does not exist!', $modelIds
            ) );
        }
        return $models;
    }

}
