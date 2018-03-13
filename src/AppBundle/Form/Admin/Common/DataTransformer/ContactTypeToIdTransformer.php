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

        return $contactType->getId();
    }

    /**
     * Transforms a string (id) to an object (contactType).
     *
     * @param  string $contactTypeId
     * @return Issue|null
     * @throws TransformationFailedException if object (contactType) is not found.
     */
    public function reverseTransform( $contactTypeId )
    {
        // no contactType id? It's optional, so that's ok
        if( !$contactTypeId )
        {
            return;
        }

        if( is_numeric( $contactTypeId ) )
        {
            $contactType = $this->em
                    ->getRepository( 'AppBundle\Entity\Common\ContactType' )
                    ->find( $contactTypeId );
        }
        else
        {
            $contactType = $this->em
                    ->getRepository( 'AppBundle\Entity\Common\ContactType' )
                    ->findOneByEntity( $contactTypeId );
        }

        if( null === $contactType )
        {
            throw new TransformationFailedException( sprintf(
                    'A contact type with name "%s" does not exist!', $contactTypeId
            ) );
        }

        return $contactType;
    }

}
