<?php

Namespace AppBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Comment
{

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $comment;

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment( $comment )
    {
        $this->comment = $comment;
    }

}
