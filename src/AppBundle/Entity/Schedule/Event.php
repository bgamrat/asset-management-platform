<?php

namespace AppBundle\Entity\Schedule;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Common\Person;
use AppBundle\Entity\Client\Contract;
use AppBundle\Entity\Client\Trailer;
use AppBundle\Entity\Schedule\TimeSpan;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity()
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Event
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true, unique=true)
     * @Gedmo\Versioned
     */
    private $name;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=256, nullable=true)
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Client\Trailer", cascade={"persist"})
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
     * Set name
     *
     * @param string $name
     *
     * @return Event
     */
    public function setName( $name )
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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

    public function getContacts()
    {
        return $this->contacts->toArray();
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
        $return = [];

        foreach( $this->contracts as $c )
        {
            $ct = [];
            $ct['id'] = $c->getId();
            $ct['name'] = $c->getName();
            $ct['comment'] = $c->getComment();
            $ct['active'] = $c->isActive();
            $dt = $c->getStart();
            $ct['start'] = !empty( $dt ) ? $dt->format( 'Y-m-d' ) : null;
            $dt = $c->getEnd();
            $ct['end'] = !empty( $dt ) ? $dt->format( 'Y-m-d' ) : null;
            $ct['value'] = $c->getValue();
            $return[] = $ct;
        }
        return $return;
    }

    public function addContract( Contract $contract )
    {
        if( !$this->contracts->contains( $contract ) )
        {
            $this->contracts->add( $contract );
            $contract->setClient( $this );
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

    public function addTrailer( Trailer $trailer )
    {
        if( !$this->trailers->contains( $trailer ) )
        {
            $this->trailers->add( $trailer );
            $trailer->setClient( $this );
        }
        return $this;
    }

    public function removeTrailer( Trailer $trailer )
    {
        $this->trailers->removeElement( $trailer );
    }

    public function getTimeSpans()
    {
        return $this->timespans->toArray();
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
