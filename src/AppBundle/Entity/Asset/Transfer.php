<?php

Namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Transfer
 *
 * @ORM\Table(name="transfer")
 * @ORM\Entity()
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Asset\TransferLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * 
 */
class Transfer
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
     * @var int
     * @Gedmo\Versioned
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="TransferStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    protected $status = null;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="cost", type="float", nullable=true, unique=false)
     */
    private $cost = 0.0;
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Common\Person")
     * @ORM\JoinColumn(name="from_id", referencedColumnName="id")
     */
    private $from = null;
    /**
     * @var int
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="assets", cascade={"persist"})
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    protected $source_location = null;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="source_location_text", type="string", length=64, nullable=true, unique=false)
     */
    protected $source_location_text = null;
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Common\Person")
     * @ORM\JoinColumn(name="to_id", referencedColumnName="id")
     */
    private $to = null;
    /**
     * @var int
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="assets", cascade={"persist"})
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    protected $destination_location = null;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="destination_location_text", type="string", length=64, nullable=true, unique=false)
     */
    protected $destination_location_text = null;
    /**
     * @var ArrayCollection $items
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Asset\TransferItem", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     * @ORM\JoinTable(name="transfer_item_item",
     *      joinColumns={@ORM\JoinColumn(name="transfer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="item_id", referencedColumnName="id", unique=false, nullable=true)}
     *      )
     */
    private $items;
    /**
     * @var ArrayCollection $bill_tos
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Client\BillTo", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     * @ORM\JoinTable(name="transfer_bill_to",
     *      joinColumns={@ORM\JoinColumn(name="transfer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="client_id", referencedColumnName="id", unique=false, nullable=true)}
     *      )
     */
    private $bill_tos;
    /**
     * @ORM\ManyToOne(targetEntity="Carrier")
     */
    private $carrier;
    /**
     * @ORM\ManyToOne(targetEntity="CarrierService")
     */
    private $carrier_service;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $tracking_number;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $instructions;
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

        return $this;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return Asset
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
     * Set from
     *
     * @param string $from
     *
     * @return Issue
     */
    public function setFrom( Person $from )
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set source_location
     *
     * @param int $source_location
     *
     * @return Asset
     */
    public function setSourceLocation( $source_location )
    {
        $this->source_location = $source_location;

        return $this;
    }

    /**
     * Get source_location
     *
     * @return SourceLocation
     */
    public function getSourceLocation()
    {
        return $this->source_location;
    }

    /**
     * Set SourceLocationText
     *
     * @param string $source_location_text
     *
     * @return Asset
     */
    public function setSourceLocationText( $source_location_text )
    {
        $this->source_location_text = $source_location_text;

        return $this;
    }

    /**
     * Get SourceLocationText
     *
     * @return string
     */
    public function getSourceLocationText()
    {
        return $this->source_location_text;
    }

    /**
     * Set to
     *
     * @param string $to
     *
     * @return Issue
     */
    public function setTo( Person $to )
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set destination_location
     *
     * @param int $destination_location
     *
     * @return Asset
     */
    public function setDestinationLocation( $destination_location )
    {
        $this->destination_location = $destination_location;

        return $this;
    }

    /**
     * Get destination_location
     *
     * @return DestinationLocation
     */
    public function getDestinationLocation()
    {
        return $this->destination_location;
    }

    /**
     * Set DestinationLocationText
     *
     * @param string $destination_location_text
     *
     * @return Asset
     */
    public function setDestinationLocationText( $destination_location_text )
    {
        $this->destination_location_text = $destination_location_text;

        return $this;
    }

    /**
     * Get DestinationLocationText
     *
     * @return string
     */
    public function getDestinationLocationText()
    {
        return $this->destination_location_text;
    }

    /**
     * Get items
     *
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items->toArray();
    }

    public function setItems( $items )
    {
        foreach( $items as $a )
        {
            $this->addItems( $a );
            $a->setTransfer( $this );
        }
        return $this;
    }

    public function addItem( Model $item )
    {
        if( !$this->extends->contains( $item ) )
        {
            $this->extends->add( $item );
            $a->setTransfer( $this );
        }
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
            $a->setTransfer( $this );
        }
        return $this;
    }

    public function addBillTo( Model $bill_to )
    {
        if( !$this->extends->contains( $bill_to ) )
        {
            $this->extends->add( $bill_to );
            $a->setTransfer( $this );
        }
    }

    /**
     * Set carrier
     *
     * @param $carrier
     *
     * @return Transfer
     */
    public function setCarrier( $carrier )
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * Get carrier
     *
     * @return Carrier
     */
    public function getCarrier()
    {
        return $this->carrier;
    }
    
    
    /**
     * Set carrier_service
     *
     * @param $carrier_service
     *
     * @return Transfer
     */
    public function setCarrierService( $carrier_service )
    {
        $this->carrier_service = $carrier_service;

        return $this;
    }

    /**
     * Get carrier_service
     *
     * @return CarrierService
     */
    public function getCarrierService()
    {
        return $this->carrier_service;
    }

    /**
     * Set tracking_number
     *
     * @param string $tracking_number
     *
     * @return Transfer
     */
    public function setTrackingNumber( $tracking_number )
    {
        $this->tracking_number = $tracking_number;

        return $this;
    }

    /**
     * Get tracking_number
     *
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->tracking_number;
    }

    /**
     * Set instructions
     *
     * @param string $instructions
     *
     * @return Transfer
     */
    public function setInstructions( $instructions )
    {
        $this->instructions = $instructions;

        return $this;
    }

    /**
     * Get instructions
     *
     * @return string
     */
    public function getInstructions()
    {
        return $this->instructions;
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
