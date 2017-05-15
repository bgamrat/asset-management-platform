<?php

namespace AppBundle\Controller\Api\Common;

class Common
{

    public function getContacts( $entities )
    {
        $data = [];
        foreach( $entities as $e )
        {
            $contacts = $e->getContacts();
            if( !empty( $contacts ) )
            {
                foreach( $contacts as $c )
                {
                    $data[] = $this->getContactDetails( $c, $e );
                }
            }
            return $data;
        }
    }

    function getContactDetails( $contact, $entity = null )
    {
        $d = [];
        if( $entity !== null )
        {
            $id = $entity->getId();
            $name = $entity->getName();
        }
        else
        {
            $id = $contact->getId();
            $name = $contact->getFullName();
        }
        $d['id'] = $id;
        $d['name'] = $name;
        $phoneLines = $contact->getPhoneLines();
        if( count( $phoneLines ) > 0 )
        {
            $phoneLines = implode( '<br>', $phoneLines ) . '<br>';
        }
        else
        {
            $phoneLines = '';
        }
        $emailLines = $contact->getEmailLines();
        if( count( $emailLines ) > 0 )
        {
            $emailLines = implode( '<br>', $emailLines ) . '<br>';
        }
        else
        {
            $emailLines = '';
        }

        // HTML label attributes for dijit.FilteringSelects MUST start with a tag
        $d['label'] = '<div>' . $name . '<br>' . $contact->getFullName() . '<br>'
                . $phoneLines
                . $emailLines;
        $addresses = $contact->getAddresses();
        if( !empty( $addresses ) )
        {
            foreach( $addresses as $a )
            {
                $d['label'] .= nl2br( $a->getAddress() );
            }
        }
        $d['label'] .= '</div>';
        return $d;
    }

}
