<?php

namespace AppBundle\Entity\Schedule;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Common\Person;
use AppBundle\Entity\Client\Contract;
use AppBundle\Entity\Common\CategoryQuantity;
use AppBundle\Entity\Asset\Trailer;
use AppBundle\Entity\Schedule\TimeSpan;
use AppBundle\Entity\Venue\Venue;
use AppBundle\Entity\Traits\Versioned\Comment;
use AppBundle\Entity\Traits\Versioned\Cost;
use AppBundle\Entity\Traits\Versioned\CustomAttributes;
use AppBundle\Entity\Traits\Versioned\Name;
use AppBundle\Entity\Traits\Versioned\Value;
use AppBundle\Entity\Traits\History;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity()
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Schedule\EventLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Event
{

    use Comment,
        Cost,
        Name,
        Value,
        TimestampableEntity,
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
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="startdate", type="datetime", nullable=true, unique=false)
     */
    private $start = null;
    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="enddate", type="datetime", nullable=true, unique=false)
     */
    private $end = null;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="tentative", type="boolean")
     */
    private $tentative = false;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="billable", type="boolean")
     */
    private $billable = true;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="canceled", type="boolean")
     */
    private $canceled = false;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client\Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
     * @Gedmo\Versioned
     */
    private $client;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Venue\Venue")
     * @ORM\JoinColumn(name="venue_id", referencedColumnName="id", nullable=true)
     * @Gedmo\Versioned
     */
    private $venue;
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Common\Person", cascade={"persist"})
     * @ORM\JoinTable(name="event_contact",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $contacts = null;
    /**
     * @var ArrayCollection $contracts
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Client\Contract", cascade={"persist"})
     * @ORM\JoinTable(name="event_contract",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contract_id", referencedColumnName="id", onDelete="CASCADE", unique=true, nullable=false)}
     *      )
     */
    protected $contracts = null;
    /**
     * @var ArrayCollection $trailers
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Asset\Trailer", cascade={"persist"})
     * @ORM\JoinTable(name="event_trailer",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="trailer_id", referencedColumnName="id", onDelete="CASCADE", unique=true, nullable=false)}
     *      )
     */
    protected $trailers = null;
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Schedule\TimeSpan", cascade={"persist"})
     * @ORM\JoinTable(name="event_time_span",
     *      joinColumns={@ORM\JoinColumn(name="time_span_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $timespans = null;
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Common\CategoryQuantity", cascade={"persist"})
     * @ORM\JoinTable(name="event_category_quantity",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")}
     *      )
     */
    private $categoryQuantities;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->contracts = new ArrayCollection();
        $this->trailers = new ArrayCollection();
        $this->time_spans = new ArrayCollection();
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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

    public function setTentative( $tentative )
    {
        $this->tentative = $tentative;
    }

    public function isTentative()
    {
        return $this->tentative;
    }

    public function setCanceled( $canceled )
    {
        $this->canceled = $canceled;
    }

    public function isCanceled()
    {
        return $this->canceled;
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
     * Set description
     *
     * @param string $description
     *
     * @return Event
     */
    public function setDescription( $description )
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set client
     *
     * @param string $client
     *
     * @return Event
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
     * Set venue
     *
     * @param string $venue
     *
     * @return Event
     */
    public function setVenue( $venue )
    {
        $this->venue = $venue;

        return $this;
    }

    /**
     * Get venue
     *
     * @return string
     */
    public function getVenue()
    {
        return $this->venue;
    }

    public function getContacts()
    {
        return empty( $this->contacts ) ? [] : $this->contacts->toArray();
    }

    public function addContact( Person $contact )
    {
        if( !$this->contacts->contains( $contact ) )
        {
            $this->contacts->add( $contact );
        }
    }

    public function removeContact( Person $contact )
    {
        $this->contacts->removeElement( $contact );
    }

    public function getContracts()
    {
        return empty( $this->contracts ) ? [] : $this->contracts->toArray();
    }

    public function addContract( Contract $contract )
    {
        if( !$this->contracts->contains( $contract ) )
        {
            $this->contracts->add( $contract );
        }
        return $this;
    }

    public function removeContract( Contract $contract )
    {
        $this->contracts->removeElement( $contract );
    }

    public function getTrailers()
    {
        $return = [];

        foreach( $this->trailers as $t )
        {
            $et = [];
            $et['id'] = $t->getId();
            $et['name'] = $t->getName();
            $return[] = $et;
        }
        return $return;
    }

    public function setTrailers( $trailers )
    {
        $this->trailers->clear();
        foreach( $trailers as $t )
        {
            $this->trailers->add( $t );
        }
        return $this;
    }

    public function setCategoryQuantities( $categoryQuantities )
    {
        $this->categoryQuantities->clear();
        foreach( $categoryQuantities as $cq )
        {
            $this->addCategoryQuantities( $cq );
        }
        return $this;
    }

    public function getCategoryQuantities( $full = true )
    {
        $categoryQuantities = [];
        if( count( $this->categoryQuantities ) > 0 )
        {
            if( $full === false )
            {
                foreach( $this->categoryQuantities as $cq )
                {
                    $categoryQuantities[] = ['id' => $cq->getId(),
                        'category' => $cq->getName(),
                        'quantity' => $cq->getQuantity()];
                }
            }
            else
            {
                foreach( $this->categoryQuantities as $cq )
                {
                    $categoryQuantities[] = $cq;
                }
            }
        }
        return $categoryQuantities;
    }

    public function addCategoryQuantity( CategoryQuantity $categoryQuantity )
    {
        if( !empty( $this->categoryQuantities ) &&
                !$this->categoryQuantities->contains( $categoryQuantity ) )
        {
            $this->categoryQuantities->add( $categoryQuantity );
        }
    }

    public function removeCategoryQuantity( CategoryQuantity $categoryQuantity )
    {
        if( !empty( $this->categoryQuantities ) )
        {
            $this->categoryQuantities->removeElement( $categoryQuantity );
        }
    }

    public function getTimeSpans()
    {
        return empty( $this->timespans ) ? [] : $this->timespans->toArray();
    }

    public function setTimeSpans( $time_spans )
    {
        foreach( $time_spans as $t )
        {
            $this->addTimeSpan( $t );
        }
        return $this;
    }

    public function addTimeSpan( TimeSpan $timespan )
    {
        if( !$this->timespans->contains( $timespan ) )
        {
            $this->timespans->add( $timespan );
        }
    }

    public function removeTimeSpan( TimeSpan $timespan )
    {
        $this->timespans->removeElement( $timespan );
    }

}
