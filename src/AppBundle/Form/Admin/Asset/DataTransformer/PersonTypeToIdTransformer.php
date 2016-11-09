<?php

namespace AppBundle\Form\Admin\Asset\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PersonTypeToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (persontype) to a string (id).
     *
     * @param  Issue|null $persontype
     * @return string
     */
    public function transform($persontype)
    {
        if (null === $persontype) {
            return '';
        }

        return $persontype->getId();
    }

    /**
     * Transforms a string (id) to an object (persontype).
     *
     * @param  string $persontypeId
     * @return Issue|null
     * @throws TransformationFailedException if object (persontype) is not found.
     */
    public function reverseTransform($persontypeId)
    {
        // no persontype id? It's optional, so that's ok
        if (!$persontypeId) {
            return;
        }

        $persontype = $this->em
            ->getRepository('AppBundle\Entity\Asset\PersonType')
            ->find($persontypeId)
        ;

        if (null === $persontype) {
            throw new TransformationFailedException(sprintf(
                'An persontype with id "%s" does not exist!',
                $persontypeId
            ));
        }
        return $persontype;
    }
}