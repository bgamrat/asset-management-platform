<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Common\Person;
use App\Entity\Asset\IssueItem;
use App\Entity\Common\BillTo;
use App\Entity\Traits\Id;
use App\Entity\Traits\Versioned\Cost;
use App\Entity\Traits\History;

/**
 * Issue
 *
 * @ORM\Table(name="issue")
 * @ORM\Entity()
 * @Gedmo\Loggable(logEntryClass="App\Entity\Asset\IssueLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Issue
{

    use Id,
        Cost,
        TimeStampableEntity,
        SoftDeleteableEntity,
        History;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var integer
     * @Gedmo\Versioned
     * @ORM\Column(name="priority", type="integer", nullable=false, unique=false)
     */
    private $priority = 3;
    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="IssueStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    protected $status = null;
    /**
     * @Gedmo\Versioned
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="IssueType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    protected $type = null;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $summary;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $details;
    /**
     * @var ArrayCollection $notes
     * @ORM\ManyToMany(targetEntity="IssueNote", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $notes;
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Trailer")
     * @ORM\JoinColumn(name="trailer_id", referencedColumnName="id")
     */
    private $trailer = null;
    /**
     * @var ArrayCollection $items
     * @ORM\OrderBy({"id" = "ASC"})
     * @ORM\ManyToMany(targetEntity="App\Entity\Asset\IssueItem", cascade={"persist","remove"}, orphanRemoval=true)
     * @ORM\JoinTable(name="issue_item_item",
     *      joinColumns={@ORM\JoinColumn(name="issue_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="item_id", referencedColumnName="id", unique=false, nullable=true)}
     *      )
     */
    protected $items;
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="App\Entity\Common\Person")
     * @ORM\JoinColumn(name="assigned_to", referencedColumnName="id")
     */
    private $assignedTo = null;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     */
    private $replaced = true;
    /**
     * @var boolean
     * @ORM\Column(name="billable", type="boolean")
     */
    private $billable = true;
    /**
     * @var ArrayCollection $bill_tos
     * @ORM\ManyToMany(targetEntity="App\Entity\Common\BillTo", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     * @ORM\JoinTable(name="issue_bill_to",
     *      joinColumns={@ORM\JoinColumn(name="issue_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id", unique=false, nullable=true)}
     *      )
     */
    private $bill_tos;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->bill_tos = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set priority
     *
     * @return integer
     */
    public function setPriority( $priority )
    {
        $this->priority = $priority;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return Issue
     */
    public function setStatus( $status )
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return Issue
     */
    public function setType( $type )
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set summary
     *
     * @param string $summary
     *
     * @return Issue
     */
    public function setSummary( $summary )
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set details
     *
     * @param string $details
     *
     * @return Issue
     */
    public function setDetails( $details )
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    public function getNotes()
    {
        return $this->notes->toArray();
    }

    public function addNote( IssueNote $note )
    {
        if( !$this->notes->contains( $note ) )
        {
            $this->notes->add( $note );
        }
    }

    public function removeNote( IssueNote $note )
    {
        $this->notes->removeElement( $note );
    }

    /**
     * Set trailer
     *
     * @param string $trailer
     *
     * @return Issue
     */
    public function setTrailer( $trailer )
    {
        $this->trailer = $trailer;

        return $this;
    }

    /**
     * Get trailer
     *
     * @return string
     */
    public function getTrailer()
    {
        return $this->trailer;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function addItem( IssueItem $item )
    {
        if( !$this->items->contains( $item ) )
        {
            $this->items->add( $item );
        }
    }

    public function removeItem( IssueItem $item )
    {
        if( !$this->items->contains( $item ) )
        {
            return;
        }

        $this->items->removeElement( $item );
    }

    /**
     * Set assignedTo
     *
     * @param string $assignedTo
     *
     * @return Issue
     */
    public function setAssignedTo( Person $assignedTo )
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    /**
     * Get assignedTo
     *
     * @return string
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    public function setReplaced( $replaced )
    {
        $this->replaced = $replaced;
    }

    public function isReplaced()
    {
        return $this->replaced;
    }

    public function setBillable( $billable )
    {
        $this->billable = $billable;
    }

    public function isBillable()
    {
        return $this->billable;
    }

    /**
     * Set cost
     *
     * @param float $cost
     *
     * @return Asset
     */
    public function setCost( $cost )
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Get bill_tos
     *
     * @return ArrayCollection
     */
    public function getBillTos()
    {
        return $this->bill_tos->toArray();
    }

    public function setBillTos( $bill_tos )
    {
        foreach( $bill_tos as $a )
        {
            $this->addBillTos( $a );
        }
        return $this;
    }

    public function addBillTos( BillTo $bill_to )
    {
        if( !$this->bill_tos->contains( $bill_to ) )
        {
            $this->bill_tos->add( $bill_to );
        }
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
