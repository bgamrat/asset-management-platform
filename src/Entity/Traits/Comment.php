<?php

Namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Comment
{

    /**
     * @var string
     * 
     * @ORM\Column(type="text", nullable=true)
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
