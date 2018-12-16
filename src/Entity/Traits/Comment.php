<?php

Namespace App\Entity\Traits;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

trait Comment
{

    /**
     * @var string
     * 
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read", "write"})
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
