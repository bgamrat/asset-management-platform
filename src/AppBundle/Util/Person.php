<?php

namespace AppBundle\Util;

use AppBundle\Entity\Person As PersonEntity;
use AppBundle\Util\Address;
use AppBundle\Util\Email;
use AppBundle\Util\PhoneNumber;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Person
{

    private $emailUtil;
    private $phoneNumberUtil;
    private $addressUtil;

    public function __construct( PhoneNumber $phoneNumberUtil, Email $emailUtil, Address $addressUtil )
    {
        $this->emailUtil = $emailUtil;
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->addressUtil = $addressUtil;
    }

    public function update( PersonEntity $person, $data )
    {
        if( $person === null )
        {
            $person = new PersonEntity();
        }
        $person->setFirstname( $data['firstname'] );
        $person->setMiddleinitial( $data['middleinitial'] );
        $person->setLastname( $data['lastname'] );
        $this->phoneNumberUtil->update( $person, $data['phone_numbers'] );
        $this->emailUtil->update( $person, $data['emails'] );
        $this->addressUtil->update( $person, $data['addresses'] );
    }

}
