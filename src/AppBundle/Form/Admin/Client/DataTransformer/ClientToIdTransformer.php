<?php

namespace AppBundle\Form\Admin\Client\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ClientToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (client) to a string (id).
     *
     * @param  Issue|null $client
     * @return string
     */
    public function transform($client)
    {
        if (null === $client) {
            return '';
        }

        return $client->getName();
    }

    /**
     * Transforms a string (id) to an object (client).
     *
     * @param  string $clientId
     * @return Issue|null
     * @throws TransformationFailedException if object (client) is not found.
     */
    public function reverseTransform($clientName)
    {
        // no client id? It's optional, so that's ok
        if (!$clientId) {
            return;
        }

        $client = $this->em
            ->getRepository('AppBundle\Entity\Client\Client')
            ->find($clientName)
        ;

        if (null === $client) {
            throw new TransformationFailedException(sprintf(
                'An client with name "%s" does not exist!',
                $clientName
            ));
        }
        return $client;
    }
}