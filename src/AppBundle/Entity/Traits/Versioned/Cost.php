<?php

Namespace AppBundle\Entity\Traits\Versioned;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait Cost
{

    /**
     * @var float
     * @ORM\Column(name="cost", type="float", nullable=true, unique=false)
     * @Gedmo\Versioned
     */
    private $cost;

    public function getCost()
    {
        return $this->cost;
    }

    public function setCost( $cost )
    {
        $this->cost = $cost;

        return $this;
    }

}
