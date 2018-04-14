<?php

Namespace App\Entity\Traits\Versioned;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait Active
{

    /**
     * @var boolean
     * @ORM\Column(name="active", type="boolean")
     * @Gedmo\Versioned
     */
    private $active = true;

    public function setActive( $active )
    {
        $this->active = $active;
        return $this;
    }

    public function isActive()
    {
        return $this->active;
    }

}
