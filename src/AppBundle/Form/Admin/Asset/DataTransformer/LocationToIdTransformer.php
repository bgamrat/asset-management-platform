<?php

namespace AppBundle\Form\Admin\Asset\DataTransformer;

use AppBundle\Entity\Location;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class LocationToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (model) to a string (id).
     *
     * @param  Issue|null $model
     * @return string
     */
    public function transform($location)
    {
        if (null === $location) {
            return '';
        }

        return $location->getId();
    }

    /**
     * Transforms a string (id) to an object (model).
     *
     * @param  string $modelId
     * @return Issue|null
     * @throws TransformationFailedException if object (model) is not found.
     */
    public function reverseTransform($locationId)
    {
        // no model id? It's optional, so that's ok
        if (!$locationId) {
            return;
        }

        $model = $this->em
            ->getRepository('AppBundle:Location')
            ->find($locationId)
        ;

        if (null === $model) {
            throw new TransformationFailedException(sprintf(
                'An location with id "%s" does not exist!',
                $locationId
            ));
        }
        return $location;
    }
}