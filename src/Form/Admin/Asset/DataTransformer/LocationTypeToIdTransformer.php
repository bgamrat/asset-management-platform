<?php

Namespace App\Form\Admin\Asset\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class LocationTypeToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (locationtype) to a string (id).
     *
     * @param  Issue|null $locationtype
     * @return string
     */
    public function transform($locationtype)
    {
        if (null === $locationtype) {
            return '';
        }

        return $locationtype->getId();
    }

    /**
     * Transforms a string (id) to an object (locationtype).
     *
     * @param  string $locationtypeId
     * @return Issue|null
     * @throws TransformationFailedException if object (locationtype) is not found.
     */
    public function reverseTransform($locationtypeId)
    {
        // no locationtype id? It's optional, so that's ok
        if (!$locationtypeId) {
            return;
        }
        $locationtype = $this->em
            ->getRepository('App\Entity\Asset\LocationType')
            ->find($locationtypeId)
        ;

        if (null === $locationtype) {
            throw new TransformationFailedException(sprintf(
                'An locationtype with id "%s" does not exist!',
                $locationtypeId
            ));
        }
        return $locationtype;
    }
}