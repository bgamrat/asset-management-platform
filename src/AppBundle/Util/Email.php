<?php

namespace AppBundle\Util;

use AppBundle\Entity\Email As EmailEntity;
use Doctrine\ORM\EntityManager;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Email
{

    private $em;

    public function __construct( EntityManager $em )
    {
        $this->em = $em;
    }

    public function update( $entity, $data )
    {
        if( $entity === null )
        {
            throw new \Exception( 'error.cannot_be_null' );
        }
        $existingEmails = $entity->getEmails();
        $existing = [];
        if( !empty( $existingEmails ) )
        {
            foreach( $existingEmails as $e )
            {
                $existing[$e->getEmail()] = $e->toArray();
            }
        }
        foreach( $data as $emailData )
        {
            if( $emailData['email'] !== '' )
            {
                $key = array_search( $emailData['email'], array_keys( $existing ), false );
                if( $key !== false )
                {
                    $email = $existingEmails[$key];
                    unset( $existingEmails[$key] );
                }
                else
                {
                    $email = new EmailEntity();
                }
                $email->setType( $emailData['type'] )
                        ->setEmail( $emailData['email'] )
                        ->setComment( $emailData['comment'] );
                $this->em->persist($email);
                if( $key === false )
                {
                    $entity->addEmail($email);
                }
            }
        }
        if( !empty( $existingEmails ) )
        {
            foreach( $existingEmails as $leftOver )
            {
                $entity->removeEmail( $leftOver );
            }
        }
    }

}