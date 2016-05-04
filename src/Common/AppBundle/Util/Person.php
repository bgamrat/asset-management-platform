<?php

namespace Common\AppBundle\Util;

use Common\AppBundle\Entity\Person As PersonEntity;
use Common\AppBundle\Entity\User;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Person
{

    public function processPersonUpdates( User $user, $data )
    {
        $person = new PersonEntity();
        $person->setFirstname( $data['firstname'] );
        $person->setMiddleinitial( $data['middleinitial'] );
        $person->setLastname( $data['lastname'] );
        $person->persist();
        $user->setPerson( $person );
    }

}
