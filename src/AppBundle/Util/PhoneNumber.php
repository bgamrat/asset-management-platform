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

    public function update( $entity, $data )
    {
        if ($entity === null) {
            throw new \Exception('error.cannot_be_null');
        }
        $existingPhoneNumbers = $entity->getPhoneNumbers();
        $existing = [];
        foreach ($existingPhoneNumbers as $p) {
            $existing[preg_replace('/\D/','',$p->getPhoneNumber())] = $p;
        }
        foreach($data as $phone) {
            $digits = preg_replace('/\D/','',$phone['phone_number']);
            if ($digits !== '') {
                $key = array_search($digits,array_keys($existing),false);
                if ($key !== false) {
                    $phoneNumber = $existing[$digits];
                    unset($existing[$digits]);
                } else {
                    $phoneNumber = new PhoneNumberEntity();
                    $entity->addPhoneNumber($phoneNumber);
                }
                $phoneNumber->setType($phone['type']);
                $phoneNumber->setPhoneNumber($phone['phone_number']);
                $phoneNumber->setComment($phone['comment']);
            }
        }
        foreach ($existing as $leftOver) {
            $entity->removePhoneNumber($leftOver);
        }
    }
}