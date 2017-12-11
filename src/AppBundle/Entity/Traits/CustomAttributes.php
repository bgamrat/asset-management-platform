<?php

Namespace AppBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CustomAttributes
{
    /**
     * @var json
     * @ORM\Column(type="json_document", options={"jsonb": true}, name="custom_attributes", nullable=true, unique=false)
     */
    private $custom_attributes;

    public function getCustomAttributes()
    {
        return $this->custom_attributes;
    }

    public function setCustomAttributes( $custom_attributes )
    {
        $this->custom_attributes = $custom_attributes;
    }
}
