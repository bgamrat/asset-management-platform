<?php

namespace AppBundle\Util;

use AppBundle\Entity\Email As EmailEntity;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Email
{

    public function update( $entity, $data )
    {
        if ($entity === null) {
            throw new \Exception('error.cannot_be_null');
        }

        $existingEmails = $entity->getEmails();
                
        $existing = [];
        foreach ($existingEmails as $e) {
            $existing[$e->getEmail()] = $e;
        }

        foreach($data as $emailData) {
            if ($emailData['email'] !== '') {
                $key = array_search($emailData['email'],array_keys($existing),false);
                if ($key !== false) {
                    $email = $existing[$digits];
                    unset($existing[$digits]);
                } else {
                    $email = new EmailEntity();
                    $entity->addEmail($email);
                }
                $email->setType($emailData['type']);
                $email->setEmail($emailData['email']);
                $email->setComment($emailData['comment']);
            }
        }
        foreach ($existing as $leftOver) {
            $entity->removeEmail($leftOver);
        }
    }
}