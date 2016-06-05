<?php

namespace AppBundle\Util;

use AppBundle\Entity\Person As ContactEntity;
use Doctrine\ORM\EntityManager;

/**
 * Description of Person
 *
 * @author bgamrat
 */
class Contact
{

    private $em;

    public function __construct( EntityManager $em )
    {
        $this->em = $em;
        $this->personModel = $this->get( 'app.model.person' );
    }

    public function update( $entity, $data )
    {
        if( $entity === null )
        {
            throw new \Exception( 'error.cannot_be_null' );
        }
        $existingContacts = $entity->getContacts();
        $existing = [];
        if( !empty( $existingContacts ) )
        {
            foreach( $existingContacts as $c )
            {
                $existing[strtolower($c->getFullName())] = $c;
            }
        }
        foreach( $data as $contact )
        {
            $name = strtolower(preg_replace( '/\s{2,}/', '', 
                    $contact['firstname'].' '.
                    $contact['middleinitial'].' '.
                    $contact['lastname']));
            if( $name !== '' )
            {
                $key = array_search( $name, array_keys( $existing ), false );
                if( $key !== false )
                {
                    $person = $existingContacts[$key];
                    unset( $existingContacts[$key] );
                }
                else
                {
                    $person = null;
                }
                $person = $this->personModel->update($person);
                if ($key === false) {
                    $entity->addContact($person);
                }
            }
        }
        if( !empty( $existingContacts ) )
        {
            foreach( $existingContacts as $leftOver )
            {
                $entity->removeContact( $leftOver );
            }
        }
    }

}
