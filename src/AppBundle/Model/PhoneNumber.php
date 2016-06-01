<?php

namespace AppBundle\Model;

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

    public function __construct( EntityManager $em)
    {
        $this->em = $em;
    }


    public function get( $phoneNumber )
    {
        $data = null;
        if( $phoneNumber !== null )
        {
            $data = $phoneNumber->toArray();
        }
        return $data;
    }

    public function update( $entity, $data )
    {
        if( $entity === null )
        {
            throw new \Exception( 'error.cannot_be_null' );
        }
        $existingPhoneNumbers = $entity->getPhoneNumbers();
        $existing = [];
        foreach( $existingPhoneNumbers as $p )
        {
            $existing[(string) preg_replace( '/\D/', '', $p->getPhoneNumber() )] = $p->toArray();
        }
        foreach( $data as $phone )
        {
            $digits = preg_replace( '/\D/', '', $phone['phone_number'] );
            if( $digits !== '' )
            {
                $key = array_search( $digits, array_keys( $existing ), false );
                if( $key !== false )
                {
                    unset( $existingPhoneNumbers[$key] );
                }
                else
                {
                    $phoneNumber = new PhoneNumberEntity();
                    $phoneNumber->setType( $phone['type'] );
                    $phoneNumber->setPhoneNumber( $phone['phone_number'] );
                    $phoneNumber->setComment( $phone['comment'] );
                    $phoneNumber->setPerson($entity);
                    $this->em->persist($phoneNumber);
                    $entity->addPhoneNumber( $phoneNumber );
                }
            }
        }
        foreach( $existingPhoneNumbers as $leftOver )
        {
            $entity->removePhoneNumber( $leftOver );
        }
        $this->em->persist($entity);
    }

}
