<?php

Namespace AppBundle\Entity\Client;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Asset\Trailer")
     * @ORM\JoinColumn(name="trailer_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $trailer;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="value", type="float", nullable=true, unique=false)
     */
    private $value = 0.0;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Gedmo\Versioned
     */
    private $comment;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @return integer
     */
    public function setId( $id )
    {
        $this->id = $id;

        return $this;
    }

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

    public function setValue( $value )
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Trailer
     */
    public function setComment( $comment )
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

}
