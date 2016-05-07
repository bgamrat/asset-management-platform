<?php

namespace AppBundle\Util;

use AppBundle\Entity\Person As PersonEntity;
use AppBundle\Entity\User;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Person
{

    public function processPersonUpdates( User $user, $data )
    {
        $person = $user->getPerson();
        if ($person === null) {
            $person = new PersonEntity();
        }
        $person->setFirstname( $data['firstname'] );
        $person->setMiddleinitial( $data['middleinitial'] );
        $person->setLastname( $data['lastname'] );
        $user->setPerson($person);
    }

}
