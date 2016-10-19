<?php

namespace AppBundle\Form\Admin\Asset\DataTransformer;

use AppBundle\Entity\Asset\Model;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ModelRelationshipsToIdsTransformer implements DataTransformerInterface
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
        if( empty($models) )
        {
            return null;
        }
        $ret = [];
        foreach ($models as $m) {
            $ret[] = $m->getId();
        }
        return $ret;
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
        if( empty( $modelIds ) )
        {
            return [];
        }

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select( 'm' )
                ->from( 'AppBundle:Model', 'm' )
                ->where( $queryBuilder->expr()->in( 'm.id', ':modelIds' ) )
                ->setParameter( 'modelIds', $modelIds )
                ->orderBy( 'm.name', 'ASC' );
        $models = $queryBuilder->getQuery()->getResult();

        if( null === $models )
        {
            throw new TransformationFailedException( sprintf(
                    'An model with id "%s" does not exist!', implode(',',$modelIds)
            ) );
        }
        return $models;
    }

}
