<?php

Namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Active
{
    /**
     * @var boolean
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;

    public function setActive( $active )
    {
        $this->active = $active;
    }

    public function isActive()
    {
        return $this->active;
    }
}
