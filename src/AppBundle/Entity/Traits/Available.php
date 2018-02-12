<?php

Namespace AppBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Available
{
    /**
     * @var boolean
     * @ORM\Column(name="available", type="boolean")
     */
    private $available = true;

    public function setAvailable( $available )
    {
        $this->available = $available;
    }

    public function isAvailable()
    {
        return $this->available;
    }
}
