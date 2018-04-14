<?php

Namespace App\Entity\Traits\Versioned;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait Quantity
{

    /**
     * @var int
     * @Gedmo\Versioned
     * @ORM\Column(name="quantity", type="integer", nullable=false, unique=false)
     */
    private $quantity = 1;

    public function setQuantity( $quantity )
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function addQuantity( $quantity )
    {
        $this->quantity += $quantity;

        return $this;
    }

    public function subtractQuantity( $quantity )
    {
        $this->quantity -= $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }


}
