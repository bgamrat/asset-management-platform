<?php

Namespace AppBundle\Entity\Traits;

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
     * @param string $id
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
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}