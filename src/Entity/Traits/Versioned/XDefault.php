<?php

Namespace App\Entity\Traits\Versioned;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait XDefault
{
    /**
     * @var boolean
     * @ORM\Column(name="default_value", type="boolean")
     * @Gedmo\Versioned
     */
    private $default = false;

    public function setDefault( $default )
    {
        $this->default = $default;
        return $this;
    }

    public function isDefault()
    {
        return $this->default;
    }
}
