<?php

namespace AppBundle\Form\Admin\Asset\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CarrierServiceToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (carrierService) to a string (id).
     *
     * @param  Issue|null $carrierService
     * @return string
     */
    public function transform($carrierService)
    {
        if (null === $carrierService) {
            return '';
        }

        if (is_string($carrierService)) {
            return $carrierService;
        }
        
        return $carrierService->getName();
    }

    /**
     * Transforms a string (id) to an object (carrierService).
     *
     * @param  string $carrierServiceId
     * @return Issue|null
     * @throws TransformationFailedException if object (carrierService) is not found.
     */
    public function reverseTransform($carrierServiceName = null)
    {
        // no carrierService id? It's optional, so that's ok
        if (!$carrierServiceName) {
            return;
        }

        $carrierService = $this->em
            ->getRepository('AppBundle\Entity\Asset\carrierService')
            ->find($carrierServiceName)
        ;

        if (null === $carrierService) {
            throw new TransformationFailedException(sprintf(
                'An carrierService with name "%s" does not exist!',
                $carrierServiceName
            ));
        }
        return $carrierService;
    }
}