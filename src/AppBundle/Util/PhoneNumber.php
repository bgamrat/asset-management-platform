<?php

namespace AppBundle\Util;

use AppBundle\Entity\PhoneNumber As PhoneNumberEntity;
use AppBundle\Entity\User;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class PhoneNumber
{

    public function processPhoneNumberUpdates( $entity, $data )
    {
        if ($entity === null) {
            throw new \Exception('error.cannot_be_null');
        }
        foreach($data as $phone) {
            $phoneNumber = new PhoneNumberEntity();
            $phoneNumber->setType($phone['type']);
            $phoneNumber->setPhoneNumber($phone['phonenumber']);
            $phoneNumber->setComment($phone['comment']);
            $entity->addPhoneNumber($phoneNumber);
        }
    }

}
