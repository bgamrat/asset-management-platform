<?php

Namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Common\Person;
use AppBundle\Entity\Asset\IssueItem;
use AppBundle\Entity\Client\BillTo;

/**
 * Issue
 *
 * @ORM\Table(name="issue")
 * @ORM\Entity()
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Asset\IssueLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Issue
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
     * @ORM\ManyToMany(targetEntity="IssueNote", cascade={"persist"})
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Asset\IssueItem", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     * @ORM\JoinTable(name="issue_item_item",
     *      joinColumns={@ORM\JoinColumn(name="issue_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="item_id", referencedColumnName="id", unique=false, nullable=true)}
     *      )
     */
    protected $items;
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Common\Person")
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
     * @ORM\Column(type="boolean")
     */
    private $clientBillable = true;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="cost", type="float", nullable=true, unique=false)
     */
    private $cost = 0.0;
    /**
     * @var ArrayCollection $bill_tos
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Client\BillTo", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     * @ORM\JoinTable(name="issue_bill_to",
     *      joinColumns={@ORM\JoinColumn(name="issue_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="client_id", referencedColumnName="id", unique=false, nullable=true)}
     *      )
     */
    private $bill_tos;
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

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->bill_tos = new ArrayCollection();
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
     * Set id
     *
     * @return integer
     */
    public function setId( $id )
    {
        $this->id = $id;
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
        return $this->items->toArray();
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

    public function setClientBillable( $clientBillable )
    {
        $this->clientBillable = $clientBillable;
    }

    public function isClientBillable()
    {
        return $this->clientBillable;
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

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated( $created )
    {
        $this->created = $created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpdated( $updated )
    {
        $this->updated = $updated;
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
