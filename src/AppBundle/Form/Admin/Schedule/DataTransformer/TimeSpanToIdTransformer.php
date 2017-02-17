<?php

namespace AppBundle\Form\Admin\Schedule\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TimeSpanToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (timeSpan) to a string (id).
     *
     * @param  Issue|null $timeSpan
     * @return string
     */
    public function transform($timeSpan)
    {
        if (null === $timeSpan) {
            return '';
        }

        return $timeSpan->getName();
    }

    /**
     * Transforms a string (id) to an object (timeSpan).
     *
     * @param  string $timeSpanId
     * @return Issue|null
     * @throws TransformationFailedException if object (timeSpan) is not found.
     */
    public function reverseTransform($timeSpanId)
    {
        // no timeSpan id? It's optional, so that's ok
        if (!$timeSpanId) {
            return;
        }

        $timeSpan = $this->em
            ->getRepository('AppBundle\Entity\Schedule\TimeSpan')
            ->find($timeSpanId)
        ;

        if (null === $timeSpan) {
            throw new TransformationFailedException(sprintf(
                'An timeSpan with id "%s" does not exist!',
                $timeSpanId
            ));
        }
        return $timeSpan;
    }
}