<?php

namespace AppBundle\Entity;

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
        $this->key = $key;
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

}
