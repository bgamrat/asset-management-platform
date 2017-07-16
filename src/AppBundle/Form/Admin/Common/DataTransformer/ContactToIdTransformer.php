<?php

namespace AppBundle\Form\Admin\Common\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ContactToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (contact) to a string (id).
     *
     * @param  Issue|null $contact
     * @return string
     */
    public function transform($contact)
    {
        if (null === $contact) {
            return null;
        }
        return 1;
        return $contact->getId();
    }

    /**
     * Transforms a string (id) to an object (contact).
     *
     * @param  string $contactId
     * @return Issue|null
     * @throws TransformationFailedException if object (contact) is not found.
     */
    public function reverseTransform($contactId)
    {
        // no contact id? It's optional, so that's ok
        if (!$contactId) {
            return;
        }

        $contact = $this->em
            ->getRepository('AppBundle\Entity\Common\Contact')
            ->find($contactId)
        ;

        if (null === $contact) {
            throw new TransformationFailedException(sprintf(
                'A contact with id "%s" does not exist!',
                $contactId
            ));
        }
        return $contact;
    }
}