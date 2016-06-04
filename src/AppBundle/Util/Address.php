<?php

namespace AppBundle\Util;

use AppBundle\Entity\Person As PersonEntity;
use AppBundle\Entity\Address As AddressEntity;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Address
{

    private $em;

    public function __construct( EntityManager $em )
    {
        $this->em = $em;
    }

    public function update( $entity, $data )
    {
        if( $entity === null )
        {
            throw new \Exception( 'error.cannot_be_null' );
        }
        $existingAddresses = $entity->getAddresses();
        $existing = [];
        if( !empty( $existingAddresses ) )
        {
            foreach( $existingAddresses as $a )
            {
                $existing[] = $a->toArray();
            }
        }
        foreach( $data as $addressData )
        {
            if( $addressData['city'] !== '' && $addressData['state_province'] !== '' )
            {
                $key = array_search( $addressData, $existing, false );
                if( $key !== false )
                {
                    $address = $existingAddresses[$key];
                    unset( $existingAddresses[$key] );
                }
                else
                {
                    $address = new AddressEntity();
                }
                $address->setType( $addressData['type'] );
                $address->setStreet1( $addressData['street1'] );
                $address->setStreet2( $addressData['street2'] );
                $address->setCity( $addressData['city'] );
                $address->setStateProvince( $addressData['state_province'] );
                $address->setPostalCode( $addressData['postal_code'] );
                $address->setCountry( $addressData['country'] );
                $address->setComment( $addressData['comment'] );
                $this->em->persist($address);
                if ($key === false) {
                    $entity->addAddress($address);
                }
            }
        }
        if( !empty( $existingAddresses ) )
        {
            foreach( $existingAddresses as $leftOver )
            {
                $entity->removeAddress( $leftOver );
            }
        }
    }

}
