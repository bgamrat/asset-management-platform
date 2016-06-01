<?php

namespace AppBundle\Model;

use AppBundle\Entity\Person As PersonEntity;
use AppBundle\Model\Address;
use AppBundle\Model\Email;
use AppBundle\Model\PhoneNumber;
use Doctrine\ORM\EntityManager;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Person
{

    private $em;
    private $email;
    private $phoneNumber;
    private $address;

    public function __construct( EntityManager $em, PhoneNumber $phoneNumber, Email $email, Address $address )
    {
        $this->em = $em;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->address = $address;
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
                    $data['phone_numbers'][] = $this->phoneNumber->get( $phone );
                }
            }
            $emails = $person->getEmails();
            if( $emails !== null )
            {
                $data['emails'] = [];
                foreach( $emails as $email )
                {
                    $data['emails'][] = $this->email->get( $email );
                }
            }
            $addresses = $person->getAddresses();
            if( $addresses !== null )
            {
                $data['addresses'] = [];
                foreach( $addresses as $address )
                {
                    $data['addresses'][] = $this->address->get( $address );
                }
            }
        }
        else
        {
            $data = array_fill_keys( ['type','firstname', 'middleinitial', 'lastname','comment'], '' );
        }
        return $data;
    }

    public function update( $entity = null, Array $data )
    {
        $person = $entity->getPerson();
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
        $entity->setPerson($person);
        $this->phoneNumber->update( $person, $data['phone_numbers'] );
        $this->email->update( $person, $data['emails'] );
        $this->address->update( $person, $data['addresses'] );
        
    }

}
