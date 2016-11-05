<?php

namespace AppBundle\Form\Admin\Asset\DataTransformer;

use AppBundle\Entity\Asset\Brand;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class BrandsToIdsTransformer implements DataTransformerInterface
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
    public function transform( $brands )
    {
        if( $brands === null )
        {
            return null;
        }
        $ret = [];
        foreach ($brands as $b) {
            $ret[] = $b->getId();
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
    public function reverseTransform( $brandIds )
    {
        // no model id? It's optional, so that's ok
        if( !$brandIds )
        {
            return [];
        }

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select( 'b' )
                ->from( 'AppBundle:Brand', 'b' )
                ->where( $queryBuilder->expr()->in( 'b.id', ':brand_ids' ) )
                ->setParameter( 'brand_ids', $brandIds )
                ->orderBy( 'b.name', 'ASC' );
        $brands = $queryBuilder->getQuery()->getResult();

        if( null === $brands )
        {
            throw new TransformationFailedException( sprintf(
                    'An brand with id "%s" does not exist!', implode(',',$brandIds)
            ) );
        }
        return $brands;
    }

}
