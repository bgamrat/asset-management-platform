<?php

namespace AppBundle\Model;

use AppBundle\Entity\Person As PersonEntity;
use AppBundle\Util\Address As AddressUtil;
use AppBundle\Util\Email As EmailUtil;
use AppBundle\Util\PhoneNumber As PhoneNumberUtil;
use Doctrine\ORM\EntityManager;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Person
{

    private $em;
    private $emailUtil;
    private $phoneNumberUtil;
    private $addressUtil;

    public function __construct( EntityManager $em, PhoneNumberUtil $phoneNumberUtil, EmailUtil $emailUtil, AddressUtil $addressUtil )
    {
        $this->em = $em;
        $this->emailUtil = $emailUtil;
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->addressUtil = $addressUtil;
    }

    public function get( $person )
    {
        if( $person !== null )
        {
            $data = [
                'type' => $person->getType(),
                'firstname' => $person->getFirstname(),
                'middleinitial' => $person->getMiddleinitial(),
                'lastname' => $person->getLastname(),
                'comment' => $person->getComment()
            ];
            $phoneNumbers = $person->getPhoneNumbers();
            if( $phoneNumbers !== null )
            {
                $data['phone_numbers'] = [];
                foreach( $phoneNumbers as $phone )
                {
                    $data['phone_numbers'][] = $phone->toArray();
                }
            }
            $emails = $person->getEmails();
            if( $emails !== null )
            {
                $data['emails'] = [];
                foreach( $emails as $email )
                {
                    $data['emails'][] = $email->toArray();
                }
            }
            $addresses = $person->getAddresses();
            if( $addresses !== null )
            {
                $data['addresses'] = [];
                foreach( $addresses as $address )
                {
                    $data['addresses'][] = $address->toArray();
                }
            }
        }
        else
        {
            $data = array_fill_keys( ['type','firstname', 'middleinitial', 'lastname','comment'], '' );
        }
        return $data;
    }

    public function update( $person = null, Array $data )
    {
        if( $person === null )
        {
            $person = new PersonEntity();
        } 
        $person->setType( $data['type'] );
        $person->setFirstname( $data['firstname'] );
        $person->setMiddleinitial( $data['middleinitial'] );
        $person->setLastname( $data['lastname'] );
        $person->setComment( $data['comment'] );
        $this->em->persist($person);
        $this->phoneNumberUtil->update( $person, $data['phone_numbers'] );
        $this->emailUtil->update( $person, $data['emails'] );
        $this->addressUtil->update( $person, $data['addresses'] );
        return $person;
    }

}
