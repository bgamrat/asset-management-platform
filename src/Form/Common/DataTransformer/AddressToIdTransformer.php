<?php

Namespace App\Form\Common\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use App\Entity\Common\Address;

class AddressToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (address) to a string (id).
     *
     * @param  Issue|null $address
     * @return string
     */
    public function transform($address)
    {
        if (null === $address) {
            return null;
        }

        return $address->getId();
    }

    /**
     * Transforms a string (id) to an object (address).
     *
     * @param  string $addressId
     * @return Issue|null
     * @throws TransformationFailedException if object (address) is not found.
     */
    public function reverseTransform($addressId)
    {
        // no address id? It's optional, so that's ok
        if (!$addressId) {
            return;
        }

        $address = $this->em
            ->getRepository('App\Entity\Common\Address')
            ->find($addressId)
        ;

        if (null === $address) {
            throw new TransformationFailedException(sprintf(
                'A address with id "%s" does not exist!',
                $addressId
            ));
        }
        return $address;
    }
}