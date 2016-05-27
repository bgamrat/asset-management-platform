<?php

namespace AppBundle\Util;

use AppBundle\Entity\Email As EmailEntity;
use AppBundle\Entity\User;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Email
{

    public function processEmailUpdates( $entity, $data )
    {
        if ($entity === null) {
            throw new \Exception('error.cannot_be_null');
        }
        $existingEmails = $entity->getEmails();
        $existing = [];
        foreach ($existingEmails as $e) {
            $existing[$e->getEmail()] = $e;
        }
        foreach($data as $email) {
            $key = array_search($email['email'],array_keys($existing),false);
            if ($key !== false) {
                $email = $existing[$digits];
                unset($existing[$digits]);
            } else {
                $email = new EmailEntity();
                $entity->addEmail($email);
            }
            $email->setType($email['type']);
            $email->setEmail($email['email']);
            $email->setComment($email['comment']);
        }
        foreach ($existing as $leftOver) {
            $entity->removeEmail($leftOver);
        }
    }
}