<?php

namespace AppBundle\Entity\Schedule;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Status
 *
 * @ORM\Table(name="time_span")
 * @ORM\Entity()
 * @UniqueEntity("id")
 */
class TimeSpan
{

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Asset", mappedBy="id")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="TimeSpanType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     * @ORM\OrderBy({"type" = "ASC"})
     */
    protected $type;
    /**
     * @ORM\Column(name="startdate", type="datetime", nullable=true, unique=false)
     */
    private $start = null;
    /**
     * @ORM\Column(name="enddate", type="datetime", nullable=true, unique=false)
     */
    private $end = null;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $comment;

    /**
     * Set id
     *
     * @return integer
     */
    public function setId( $id )
    {
        $this->id = $id;
    }

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
     * Set type
     *
     * @param string $type
     *
     * @return Event
     */
    public function setType( $type )
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set start
     *
     * @param float $start
     *
     * @return Event
     */
    public function setStart( $start )
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return float
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param float $end
     *
     * @return Event
     */
    public function setEnd( $end )
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return float
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return TimeSpan
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

}
