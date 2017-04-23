<?php

Namespace AppBundle\Entity\Client;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * BillTo
 *
 * @ORM\Table(name="bill_to")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Client\BillToLog")
 * @ORM\Entity()
 * 
 */
class BillTo
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\ManyToOne(targetEntity="Transfer", inversedBy="bill_tos")
     * @ORM\JoinColumn(name="bill_to_id", referencedColumnName="id")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $client;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Schedule\Event")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $event;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Gedmo\Versioned
     */
    private $comment;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="amount", type="float", nullable=true, unique=false)
     */
    private $amount = 0.0;
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $deletedAt;

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
     * Set client
     *
     * @param string $client
     *
     * @return BillTo
     */
    public function setClient( $client )
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set event
     *
     * @param string $event
     *
     * @return BillTo
     */
    public function setEvent( $event )
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return BillTo
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

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return BillTo
     */
    public function setAmount( $amount )
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
