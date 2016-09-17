<?php

namespace AppBundle\Util;

use AppBundle\Entity\Address As AddressEntity;
use Doctrine\ORM\EntityManager;

/**
 * Description of Address
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
                $address->setType( $addressData['type'] )
                        ->setStreet1( $addressData['street1'] )
                        ->setStreet2( $addressData['street2'] )
                        ->setCity( $addressData['city'] )
                        ->setStateProvince( $addressData['state_province'] )
                        ->setPostalCode( $addressData['postal_code'] )
                        ->setCountry( $addressData['country'] )
                        ->setComment( $addressData['comment'] );
                $this->em->persist( $address );
                if( $key === false )
                {
                    $entity->addAddress( $address );
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
