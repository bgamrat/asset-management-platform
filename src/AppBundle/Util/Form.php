<?php

namespace AppBundle\Util;

use Symfony\Component\Form\Form as BaseForm;
use Symfony\Component\HttpFoundation\Request;

class Form
{

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
                    $errorMessages[] = $name . ' - ' . $item->getErrors();
                }
            }
        }
        return implode(PHP_EOL,$errorMessages);
    }

}
