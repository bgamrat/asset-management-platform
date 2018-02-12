<?php

Namespace AppBundle\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Entity\Traits\Versioned\Active;
use AppBundle\Entity\Traits\Versioned\Comment;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\Versioned\Name;
use AppBundle\Entity\Traits\Versioned\Value;

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

    use Active,
        Comment,
        Id,
        Name,
        Value,
        TimestampableEntity,
        SoftDeleteableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="issue_bill_to")
     * @ORM\ManyToOne(targetEntity="Transfer", inversedBy="transfer_bill_to")
     * @ORM\JoinColumn(name="bill_to_id", referencedColumnName="id")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Common\Contact", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $contact;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Schedule\Event")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $event;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="amount", type="float", nullable=true, unique=false)
     */
    private $amount = 0.0;

    /**
     * Set contact
     *
     * @param string $contact
     *
     * @return BillTo
     */
    public function setContact( $contact )
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
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

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
