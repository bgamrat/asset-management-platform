<?php

namespace AppBundle\Util;

use AppBundle\Form\Admin\User\UserType;
use Symfony\Component\Form\Form as BaseForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Form 
{
    public function getJsonData( Request $request )
    {
        $data = json_decode( $request->getContent(), true );
        return $data;
    }

    public function strToBool( $string ) {
        if ($string === 'true') {
            return true;
        } else {
            if ($string === 'false') {
                return false;
            }
        }
        return null;
    }
    
    public function validateFormData( BaseForm $form, $data )
    {
        $form->submit( $data );
        if( !$form->isValid() )
        {
            throw HttpException( 400, $form->getErrors( true, false ) );
        }
    }

}
