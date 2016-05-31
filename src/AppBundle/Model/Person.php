<?php

namespace AppBundle\Model;

use AppBundle\Entity\Person As PersonEntity;
use AppBundle\Model\Address;
use AppBundle\Model\Email;
use AppBundle\Model\PhoneNumber;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Person
{

    private $email;
    private $phoneNumber;
    private $address;

    public function __construct( PhoneNumber $phoneNumber, Email $email, Address $address )
    {
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->address = $address;
    }

    public function get( PersonEntity $person )
    {
        if( $person !== null )
        {
            $data = [
                'firstname' => $person->getFirstname(),
                'middleinitial' => $person->getMiddleinitial(),
                'lastname' => $person->getLastname()
            ];
            $phoneNumbers = $person->getPhoneNumbers();
            if( $phoneNumbers !== null )
            {
                $data['phone_numbers'] = [];
                foreach( $phoneNumbers as $phone )
                {
                    $data['phone_numbers'][] = $this->phoneNumber->get($phone);
                }
            }
            $emails = $person->getEmails();
            if( $emails !== null )
            {
                $data['emails'] = [];
                foreach( $emails as $email )
                {
                    $data['emails'][] = $this->email->get($email);
                }
            }
            $addresses = $person->getAddresses();
            if( $addresses !== null )
            {
                $data['addresses'] = [];
                foreach( $addresses as $address )
                {
                    $data['addresses'][] = $this->address->get($address);
                }
            }
        }
        else
        {
            $data = array_fill_keys( ['firstname', 'middleinitial', 'lastname'], '' );
        }
        return $data;
    }

    public function update( PersonEntity $person, Array $data )
    {
        if( $person === null )
        {
            $person = new PersonEntity();
        }
        $person->setFirstname( $data['firstname'] );
        $person->setMiddleinitial( $data['middleinitial'] );
        $person->setLastname( $data['lastname'] );
        $this->phoneNumber->update( $person, $data['phone_numbers'] );
        $this->email->update( $person, $data['emails'] );
        $this->address->update( $person, $data['addresses'] );
    }

}
