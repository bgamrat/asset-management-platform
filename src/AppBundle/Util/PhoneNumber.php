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
        $existingPhoneNumbers = $entity->getPhonenumbers();
        $existing = [];
        foreach ($existingPhoneNumbers as $p) {
            $existing[preg_replace('/\D/','',$p->getPhonenumber())] = $p;
        }
        foreach($data as $phone) {
            $digits = preg_replace('/\D/','',$phone['phonenumber']);
            $key = array_search($digits,array_keys($existing),false);
            if ($key !== false) {
                $phoneNumber = $existing[$digits];
                unset($existing[$digits]);
            } else {
                $phoneNumber = new PhoneNumberEntity();
                $entity->addPhonenumber($phoneNumber);
            }
            $phoneNumber->setType($phone['type']);
            $phoneNumber->setPhoneNumber($phone['phonenumber']);
            $phoneNumber->setComment($phone['comment']);
        }
        foreach ($existing as $leftOver) {
            $entity->removePhonenumber($leftOver);
        }
    }
}