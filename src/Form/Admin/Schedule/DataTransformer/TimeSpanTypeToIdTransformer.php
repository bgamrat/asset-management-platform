<?php

Namespace App\Form\Admin\Schedule\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TimeSpanTypeToIdTransformer implements DataTransformerInterface
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
    public function transform($timeSpanType)
    {
        if (null === $timeSpanType) {
            return '';
        }

        return $timeSpanType->getName();
    }

    /**
     * Transforms a string (id) to an object (timeSpan).
     *
     * @param  string $timeSpanId
     * @return Issue|null
     * @throws TransformationFailedException if object (timeSpan) is not found.
     */
    public function reverseTransform($timeSpanTypeId)
    {
        // no timeSpan id? It's optional, so that's ok
        if (!$timeSpanTypeId) {
            return;
        }

        $timeSpanType = $this->em
            ->getRepository('Entity\Schedule\TimeSpanType')
            ->find($timeSpanTypeId)
        ;

        if (null === $timeSpanType) {
            throw new TransformationFailedException(sprintf(
                'An time span type with id "%s" does not exist!',
                $timeSpanTypeId
            ));
        }
        return $timeSpanType;
    }
}