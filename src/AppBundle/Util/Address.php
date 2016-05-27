<?php

namespace AppBundle\Util;

use AppBundle\Entity\Address As AddressEntity;
use AppBundle\Entity\User;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Address
{

    public function processAddressUpdates( $entity, $data )
    {
        if( $entity === null )
        {
            throw new \Exception( 'error.cannot_be_null' );
        }
        $existingAddresses = $entity->getAddresss();
        $existing = [];
        foreach( $existingAddresss as $p )
        {
            $existing[] = $p;
        }
        foreach( $data as $address )
        {
            $key = array_search( $address, $existing, false );
            if( $key !== false )
            {
                $address = $existing[$key];
                unset( $existing[$key] );
            }
            else
            {
                $address = new AddressEntity();
                $entity->addAddress( $address );
                $address->setType( $address['type'] );
                $address->setStreet1( $address['street1'] );
                $address->setStreet2( $address['street2'] );
                $address->setCity( $address['city'] );
                $address->setState( $address['state'] );
                $address->setPostalCode( $address['postal_code'] );
                $address->setCountry( $address['country'] );
                $address->setComment( $address['comment'] );
            }
        }
        foreach( $existing as $leftOver )
        {
            $entity->removeAddress( $leftOver );
        }
    }

}
