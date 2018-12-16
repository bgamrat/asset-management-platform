<?php

Namespace App\Entity\Traits;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

trait Available
{
    /**
     * @var boolean
     * @ORM\Column(name="available", type="boolean")
     * @Groups({"read", "write"})
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
