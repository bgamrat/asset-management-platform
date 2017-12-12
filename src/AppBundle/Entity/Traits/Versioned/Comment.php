<?php

Namespace AppBundle\Entity\Traits\Versioned;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait Comment
{

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned
     */
    private $comment;

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment( $comment )
    {
        $this->comment = $comment;
        return $this;
    }

}
