<?php

Namespace App\Form\Admin\Client\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ContractToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (contract) to a string (id).
     *
     * @param  Issue|null $contract
     * @return string
     */
    public function transform($contract)
    {
        if (null === $contract) {
            return '';
        }
        if (is_object($contract )) {
            return $contract->getId();
        } else {
            return $contract['id'];
        }
    }

    /**
     * Transforms a string (id) to an object (contract).
     *
     * @param  string $contractId
     * @return Issue|null
     * @throws TransformationFailedException if object (contract) is not found.
     */
    public function reverseTransform($contractId)
    {
        // no contract id? It's optional, so that's ok
        if (!$contractId) {
            return;
        }

        $contract = $this->em
            ->getRepository('Entity\Client\Contract')
            ->find($contractId)
        ;

        if (null === $contract) {
            throw new TransformationFailedException(sprintf(
                'An contract with name "%s" does not exist!',
                $contractId
            ));
        }
        return $contract;
    }
}