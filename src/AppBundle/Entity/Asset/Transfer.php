<?php

Namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Entity\Common\Person;
use AppBundle\Entity\Traits\Versioned\Cost;
use AppBundle\Entity\Traits\History;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Common\BillTo;

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

    use Cost,
        Id,
        History,
        SoftDeleteableEntity,
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
     * @var int
     * @Gedmo\Versioned
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="TransferStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    protected $status = null;
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Common\Person")
     * @ORM\JoinColumn(name="from_id", referencedColumnName="id")
     */
    private $from = null;
    /**
     * @var int
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="Location", cascade={"persist"})
     * @ORM\JoinColumn(name="source_location_id", referencedColumnName="id")
     */
    protected $source_location = null;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="source_location_text", type="string", nullable=true, unique=false)
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
     * @ORM\ManyToOne(targetEntity="Location", cascade={"persist"})
     * @ORM\JoinColumn(name="destination_location_id", referencedColumnName="id")
     */
    protected $destination_location = null;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="destination_location_text", type="string", nullable=true, unique=false)
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Common\BillTo", cascade={"persist"})
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

    public function __construct()
    {
        $this->bill_tos = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return Transfer
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
     * Set from
     *
     * @param string $from
     *
     * @return Transfer
     */
    public function setFrom( Person $from = null )
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
     * @return Transfer
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
     * @return Transfer
     */
    public function setSourceLocationText( $source_location_text )
    {
        $this->source_location_text = preg_replace( '/\n+/', PHP_EOL, str_replace( ['<br />', '<br>'], PHP_EOL, $source_location_text ) );

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
     * @param Person $to
     *
     * @return Transfer
     */
    public function setTo( Person $to = null )
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to
     *
     * @return Person
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
     * @return Transfer
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
        $this->destination_location_text = preg_replace( '/\n+/', PHP_EOL, str_replace( ['<br />', '<br>'], PHP_EOL, $destination_location_text ) );

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
        }
        return $this;
    }

    public function addItems( TransferItem $item )
    {
        if( !$this->items->contains( $item ) )
        {
            $this->items->add( $item );
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
        foreach( $bill_tos as $bt )
        {
            $this->addBillTos( $bt );
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

}
