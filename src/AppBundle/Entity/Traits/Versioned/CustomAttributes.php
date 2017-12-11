<?php

Namespace AppBundle\Entity\Traits\Versioned;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait CustomAttributes
{
    /**
     * @var json
     * @ORM\Column(type="json_document", options={"jsonb": true}, name="custom_attributes", nullable=true, unique=false)
     * @Gedmo\Versioned
     */
    private $custom_attributes;

    public function getCustomAttributes()
    {
        return $this->custom_attributes;
    }

    public function setCustomAttributes( $custom_attributes )
    {
        $this->custom_attributes = $custom_attributes;
        
        return $this;
    }
}
