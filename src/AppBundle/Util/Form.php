<?php

namespace AppBundle\Util;

use Symfony\Component\Form\Form as BaseForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler;

class Form
{

    private $session;

    public function __construct( $sessionHandler )
    {
        $storage = new NativeSessionStorage( array(), $sessionHandler );
        $this->session = new Session( $storage );
    }

    public function getJsonData( Request $request )
    {
        $data = json_decode( $request->getContent(), true );
        return $data;
    }

    public function strToBool( $string )
    {
        if( in_array( $string, [true, 'true', 'on', 1, '1', 'enabled'] ) )
        {
            return true;
        }
        else
        {
            if( in_array( $string, [false, 'false', 'off', 0, '0', 'disabled'] ) )
            {
                return false;
            }
        }
        return null;
    }

    public function getErrorMessages( BaseForm $form )
    {
        $errorMessages = [];
        if( !$form->isValid() )
        {
            $formData = $form->all();
            foreach( $formData as $name => $item )
            {
                if( !$item->isValid() )
                {
                    $errorMessages[] = $name . ' - ' . $item->getErrors(true);
                }
            }
        }
        return implode( PHP_EOL, $errorMessages );
    }

    public function saveDataTimestamp( $id, $timestamp )
    {

        $this->session->set( $id . 'timestamp', $timestamp );
    }

    public function checkDataTimestamp( $id, $timestamp )
    {
        return $timestamp !== $this->session->get( $id . 'timestamp' );
    }

}
