<?php

Namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Quantity
{

    /**
     * @var int
     *
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
