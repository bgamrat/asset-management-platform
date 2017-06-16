<?php

namespace AppBundle\Form\Admin\Venue\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class VenueToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (venue) to a string (id).
     *
     * @param  Issue|null $venue
     * @return string
     */
    public function transform($venue)
    {
        if (null === $venue) {
            return '';
        }

        return $venue->getName();
    }

    /**
     * Transforms a string (id) to an object (venue).
     *
     * @param  string $venueId
     * @return Issue|null
     * @throws TransformationFailedException if object (venue) is not found.
     */
    public function reverseTransform($venueId)
    {
        // no venue id? It's optional, so that's ok
        if (!$venueId) {
            return;
        }

        $venue = $this->em
            ->getRepository('AppBundle\Entity\Venue\Venue')
            ->find($venueId)
        ;

        if (null === $venue) {
            throw new TransformationFailedException(sprintf(
                'An venue with id "%s" does not exist!',
                $venueId
            ));
        }
        return $venue;
    }
}