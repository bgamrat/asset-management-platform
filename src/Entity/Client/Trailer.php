<?php

Namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Entity\Traits\Versioned\Active;
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Id;
use App\Entity\Traits\Versioned\Name;
use App\Entity\Traits\Versioned\Value;

/**
 * Trailer
 *
 * @ORM\Table(name="client_trailer")
 * @Gedmo\Loggable
 * @ORM\Entity()
 * 
 */
class Trailer
{

    use Active,
        Comment,
        Value,
        TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="\Entity\Asset\Trailer")
     * @ORM\JoinColumn(name="trailer_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $trailer;

    /**
     * Set trailer
     *
     * @param string $trailer
     *
     * @return Trailer
     */
    public function setTrailer( $trailer )
    {
        $this->trailer = $trailer;

        return $this;
    }

    /**
     * Get trailer
     *
     * @return Trailer
     */
    public function getTrailer()
    {
        return $this->trailer;
    }

}
