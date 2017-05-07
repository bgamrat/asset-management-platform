<?php

namespace AppBundle\Form\Common\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use AppBundle\Entity\Common\Person;

class PersonToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (person) to a string (id).
     *
     * @param  Issue|null $person
     * @return string
     */
    public function transform($person)
    {
        if (null === $person) {
            return null;
        }

        return $person->getId();
    }

    /**
     * Transforms a string (id) to an object (person).
     *
     * @param  string $personId
     * @return Issue|null
     * @throws TransformationFailedException if object (person) is not found.
     */
    public function reverseTransform($personId)
    {
        // no person id? It's optional, so that's ok
        if (!$personId) {
            return;
        }

        $person = $this->em
            ->getRepository('AppBundle\Entity\Common\Person')
            ->find($personId)
        ;

        if (null === $person) {
            throw new TransformationFailedException(sprintf(
                'A person with id "%s" does not exist!',
                $personId
            ));
        }
        return $person;
    }
}