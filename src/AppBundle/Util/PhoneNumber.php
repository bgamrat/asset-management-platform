<?php

namespace AppBundle\Util;

use AppBundle\Entity\PhoneNumber As PhoneNumberEntity;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class PhoneNumber
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
        $existingPhoneNumbers = $entity->getPhoneNumbers();
        $existing = [];
        if( !empty( $existingPhoneNumbers ) )
        {
            foreach( $existingPhoneNumbers as $p )
            {
                $existing[(string) preg_replace( '/\D/', '', $p->getPhoneNumber() )] = $p->toArray();
            }
        }
        foreach( $data as $phone )
        {
            $digits = preg_replace( '/\D/', '', $phone['phone_number'] );
            if( $digits !== '' )
            {
                $key = array_search( $digits, array_keys( $existing ), false );
                if( $key !== false )
                {
                    $phoneNumber = $existingPhoneNumbers[$key];
                    unset( $existingPhoneNumbers[$key] );
                }
                else
                {
                    $phoneNumber = new PhoneNumberEntity();
                }
                $phoneNumber->setType( $phone['type'] );
                $phoneNumber->setPhoneNumber( $phone['phone_number'] );
                $phoneNumber->setComment( $phone['comment'] );
                $this->em->persist($phoneNumber);
                if ($key === false) {
                    $entity->addPhoneNumber($phoneNumber);
                }
            }
        }
        if( !empty( $existingPhoneNumbers ) )
        {
            foreach( $existingPhoneNumbers as $leftOver )
            {
                $entity->removePhoneNumber( $leftOver );
            }
        }
    }

}
