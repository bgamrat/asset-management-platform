<?php

Namespace AppBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait InUse
{
    /**
     * @var boolean
     * @ORM\Column(name="in_use", type="boolean")
     */
    private $in_use = true;

    public function setInUse( $in_use )
    {
        $this->in_use = $in_use;
    }

    public function isInUse()
    {
        return $this->in_use;
    }
}
