<?php

Namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * CustomAttribute
 *
 * @author bgamrat
 */
class CustomAttribute
{

    private $key;
    private $value;

    public function setKey( $key )
    {
        $this->key = strtolower( $key );
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setValue( $value )
    {
        $this->value = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @Assert\IsTrue(message = "invalid_expiration_date")
     */
    public function isValueValidExpiration()
    {
        if( $this->key === 'expiration' )
        {
            return preg_match( '/^\d{4}-\d{1,2}-\d{1,2}$/', $this->value ) === 1;
        }
        return true;
    }

    /**
     * @Assert\IsTrue(message = "invalid_channels")
     */
    public function isValueValidChannels()
    {
        if( $this->key === 'channels' )
        {
            if (is_numeric( $this->value )) {
                $this->value = intval($this->value);
                return true;
            }
            return false;
        }
        return true;
    }

}
