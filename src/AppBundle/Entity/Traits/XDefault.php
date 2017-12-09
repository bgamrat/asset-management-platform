<?php

Namespace AppBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait XDefault
{
    /**
     * @var boolean
     * @ORM\Column(name="default", type="boolean")
     */
    private $default = true;

    public function setDefault( $default )
    {
        $this->default = $default;
    }

    public function isDefault()
    {
        return $this->default;
    }
}
