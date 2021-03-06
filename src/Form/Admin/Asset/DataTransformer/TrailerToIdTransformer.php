<?php

Namespace App\Form\Admin\Asset\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TrailerToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (trailer) to a string (id).
     *
     * @param  Issue|null $trailer
     * @return string
     */
    public function transform($trailer)
    {
        if (null === $trailer) {
            return '';
        }

        if (is_string($trailer)) {
            return $trailer;
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
    public function reverseTransform($trailerName = null)
    {
        // no trailer id? It's optional, so that's ok
        if (!$trailerName) {
            return;
        }

        $trailer = $this->em
            ->getRepository('App\Entity\Asset\Trailer')
            ->findOneByName($trailerName)
        ;

        if (null === $trailer) {
            throw new TransformationFailedException(sprintf(
                'An trailer with name "%s" does not exist!',
                $trailerName
            ));
        }
        return $trailer;
    }
}