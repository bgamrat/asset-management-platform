<?php

namespace AppBundle\Form\Admin\Common\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ContactTypeToIdTransformer implements DataTransformerInterface
{

    private $em;

    public function __construct( EntityManager $em )
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (contactType) to a string (id).
     *
     * @param  Issue|null $contactType
     * @return string
     */
    public function transform( $contactType )
    {
        if( null === $contactType )
        {
            return null;
        }
        return $contactType->getEntity();
    }

    /**
     * Transforms a string (id) to an object (contactType).
     *
     * @param  string $contactTypeId
     * @return Issue|null
     * @throws TransformationFailedException if object (contactType) is not found.
     */
    public function reverseTransform( $contactEntityName )
    {
        // no contactType id? It's optional, so that's ok
        if( !$contactEntityName )
        {
            return;
        }

        $contactType = $this->em
                ->getRepository( 'AppBundle\Entity\Common\ContactType' )
                ->findOneBy( ['entity' => $contactEntityName] )
        ;

        if( null === $contactType )
        {
            throw new TransformationFailedException( sprintf(
                    'A contact type with name "%s" does not exist!', $contactTypeId
            ) );
        }

        return $contactType;
    }

}
