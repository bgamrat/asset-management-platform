<?php

Namespace App\Entity\Traits;

use Symfony\Component\Serializer\Annotation\Groups;
trait Id
{

    /**
     * The id property is defined in the Entity to avoid conflicts
     *
     * The trait is providing the setter and getter methods
     */
    
    /**
     * Set id
     *
     * @param int $id
     *
     * @return Status
     */
    public function setId( $id )
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}