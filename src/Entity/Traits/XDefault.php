<?php

Namespace App\Entity\Traits;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

trait XDefault
{
    /**
     * @var boolean
     * @ORM\Column(name="default_value", type="boolean")
     * @Groups({"read", "write"})
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
