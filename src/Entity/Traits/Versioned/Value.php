<?php

Namespace App\Entity\Traits\Versioned;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait Value
{

    /**
     * @var float
     * @ORM\Column(name="value", type="float", nullable=true, unique=false)
     * @Gedmo\Versioned
     */
    private $value;

    public function getValue()
    {
        return $this->value;
    }

    public function setValue( $value )
    {
        $this->value = $value;
        return $this;
    }

}
