<?php

Namespace App\Form\Admin\Asset\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class VendorToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (vendor) to a string (id).
     *
     * @param  Issue|null $vendor
     * @return string
     */
    public function transform($vendor)
    {
        if (null === $vendor) {
            return '';
        }

        return $vendor->getId();
    }

    /**
     * Transforms a string (id) to an object (vendor).
     *
     * @param  string $vendorId
     * @return Issue|null
     * @throws TransformationFailedException if object (vendor) is not found.
     */
    public function reverseTransform($vendorId)
    {
        // no vendor id? It's optional, so that's ok
        if (!$vendorId) {
            return;
        }

        $vendor = $this->em
            ->getRepository('Entity\Asset\Vendor')
            ->find($vendorId)
        ;

        if (null === $vendor) {
            throw new TransformationFailedException(sprintf(
                'An vendor with id "%s" does not exist!',
                $vendorId
            ));
        }
        return $vendor;
    }
}