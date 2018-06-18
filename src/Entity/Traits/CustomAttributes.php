<?php

Namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CustomAttributes
{
    /**
     * @var json
     * @ORM\Column(type="json_document", options={"jsonb": true})
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
