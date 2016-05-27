<?php

namespace AppBundle\Util;

use AppBundle\Entity\Person As PersonEntity;
use AppBundle\Entity\User;
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

    public function processPersonUpdates( User $user, $data )
    {
        $person = $user->getPerson();
        if( $person === null )
        {
            $person = new PersonEntity();
        }
        $person->setFirstname( $data['firstname'] );
        $person->setMiddleinitial( $data['middleinitial'] );
        $person->setLastname( $data['lastname'] );
        $this->phoneNumberUtil->processPhoneNumberUpdates( $person, $data['phone_numbers'] );
        $this->emailUtil->processEmailUpdates( $person, $data['emails'] );
        $this->addressUtil->processAddressUpdates( $person, $data['addresses'] );
        $user->setPerson( $person );
    }

}
