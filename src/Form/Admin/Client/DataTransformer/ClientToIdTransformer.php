<?php

Namespace App\Form\Admin\Client\DataTransformer;

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
    public function reverseTransform($clientId)
    {
        // no client id? It's optional, so that's ok
        if (!$clientId) {
            return;
        }

        $client = $this->em
            ->getRepository('App\Entity\Client\Client')
            ->find($clientId)
        ;

        if (null === $client) {
            throw new TransformationFailedException(sprintf(
                'An client with id "%s" does not exist!',
                $clientId
            ));
        }
        return $client;
    }
}