<?php

Namespace App\Form\Admin\Schedule\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EventToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (event) to a string (id).
     *
     * @param  Issue|null $event
     * @return string
     */
    public function transform($event)
    {
        if (null === $event) {
            return '';
        }

        return $event->getName();
    }

    /**
     * Transforms a string (id) to an object (event).
     *
     * @param  string $eventId
     * @return Issue|null
     * @throws TransformationFailedException if object (event) is not found.
     */
    public function reverseTransform($eventId)
    {
        // no event id? It's optional, so that's ok
        if (!$eventId) {
            return;
        }

        $event = $this->em
            ->getRepository('App\Entity\Schedule\Event')
            ->find($eventId)
        ;

        if (null === $event) {
            throw new TransformationFailedException(sprintf(
                'An event with id "%s" does not exist!',
                $eventId
            ));
        }
        return $event;
    }
}