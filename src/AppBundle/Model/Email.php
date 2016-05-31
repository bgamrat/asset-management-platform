<?php

namespace AppBundle\Model;

use AppBundle\Entity\Email As EmailEntity;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Email
{

    public function get( EmailEntity $email )
    {
        $data = null;
        if( $email !== null )
        {
            $data =  $email->toArray();
        }
        return $data;
    }

    public function update( $entity, $data )
    {
        if( $entity === null )
        {
            throw new \Exception( 'error.cannot_be_null' );
        }

        $existingEmails = $entity->getEmails();

        $existing = [];
        foreach( $existingEmails as $e )
        {
            $existing[$e->getEmail()] = $e->toArray();
        }

        foreach( $data as $emailData )
        {
            if( $emailData['email'] !== '' )
            {
                $key = array_search( $emailData['email'], array_keys( $existing ), false );
                if( $key !== false )
                {
                    $email = $existing[$emailData['email']];
                    unset( $existingEmails[$key] );
                }
                else
                {
                    $email = new EmailEntity();
                    $entity->addEmail( $email );
                }
                $email->setType( $emailData['type'] );
                $email->setEmail( $emailData['email'] );
                $email->setComment( $emailData['comment'] );
            }
        }
        foreach( $existingEmails as $leftOver )
        {
            $entity->removeEmail( $leftOver );
        }
    }

}
