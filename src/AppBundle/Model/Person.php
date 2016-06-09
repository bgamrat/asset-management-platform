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
    
    // TODO: check this for best practices
    public function get( $person )
    {
        if( $person !== null )
        {
            $data = [
                'type' => $person->getType(),
                'firstname' => $person->getFirstname(),
                'middleinitial' => $person->getMiddleinitial(),
                'lastname' => $person->getLastname(),
                'fullname' => $person->getFullName(),
                'comment' => $person->getComment()
            ];
            $personType = $this->em->find('AppBundle:PersonType',['id' => $data['type']]);
            $data['typetext'] = $personType->getType();
            
            $phoneTypesData = $this->em->createQuery('SELECT pt FROM AppBundle\Entity\PhoneNumberType pt')->getResult();
            $phoneTypes = [];
            foreach ($phoneTypesData as $pt) {
                $phoneTypes[$pt->getId()] = $pt->getType();
            }
            $phoneNumbers = $person->getPhoneNumbers();
            if( $phoneNumbers !== null )
            {
                $data['phone_numbers'] = [];
                foreach( $phoneNumbers as $phone )
                {
                    $data['phone_numbers'][] = $phone->toArray() + ['typetext' => $phoneTypes[$phone->getType()]];
                }
            }
            $emailTypesData = $this->em->createQuery('SELECT e FROM AppBundle\Entity\EmailType e')->getResult();
            $emailTypes = [];
            foreach ($emailTypesData as $e) {
                $emailTypes[$e->getId()] = $e->getType();
            }
            $emails = $person->getEmails();
            if( $emails !== null )
            {
                $data['emails'] = [];
                foreach( $emails as $email )
                {
                    $data['emails'][] = $email->toArray()  + ['typetext' => $emailTypes[$email->getType()]];
                }
            }
            $addressTypesData = $this->em->createQuery('SELECT e FROM AppBundle\Entity\EmailType e')->getResult();
            $addressTypes = [];
            foreach ($addressTypesData as $e) {
                $addressTypes[$e->getId()] = $e->getType();
            }
            $addresses = $person->getAddresses();
            if( $addresses !== null )
            {
                $data['addresses'] = [];
                foreach( $addresses as $address )
                {
                    $data['addresses'][] = $address->toArray()  + ['typetext' => $addressTypes[$address->getType()]];
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
        $person->setType( $data['type'] )
                ->setFirstname( $data['firstname'] )
                ->setMiddleinitial( $data['middleinitial'] )
                ->setLastname( $data['lastname'] )
                ->setComment( $data['comment'] );
        $this->em->persist($person);
        $this->phoneNumberUtil->update( $person, $data['phone_numbers'] );
        $this->emailUtil->update( $person, $data['emails'] );
        $this->addressUtil->update( $person, $data['addresses'] );
        return $person;
    }

}
