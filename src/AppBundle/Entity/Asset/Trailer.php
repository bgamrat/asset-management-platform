<?php

Namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Asset
 *
 * @ORM\Table(name="trailer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrailerRepository")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Asset\TrailerLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
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
     * @var int
     * @Gedmo\Versioned
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="Model")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id")
     */
    protected $model = null;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="name", type="string", length=64, nullable=true, unique=false)
     */
    private $name;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="serial_number", type="string", length=64, nullable=true, unique=false)
     */
    private $serial_number;
    /**
     * @var int
     * @Gedmo\Versioned
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="AssetStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    protected $status = null;
    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="purchased", type="date", nullable=true, unique=false)
     */
    private $purchased = null;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="cost", type="float", nullable=true, unique=false)
     */
    private $cost = 0.0;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="value", type="float", nullable=true, unique=false)

     */
    private $value = 0.0;
    /**
     * @var int
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="assets", cascade={"persist"})
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    protected $location = null;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="location_text", type="string", length=64, nullable=true, unique=false)
     */
    protected $location_text = null;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $comment;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="active", type="boolean")
     * 
     */
    private $active = true;
    /**
     * @ORM\ManyToMany(targetEntity="Model", mappedBy="extends", fetch="LAZY")
     */
    private $extended_by;
    /**
     * @ORM\ManyToMany(targetEntity="Trailer", inversedBy="extended_by", fetch="LAZY")
     * @ORM\JoinTable(name="trailer_extend",
     *      joinColumns={@ORM\JoinColumn(name="extends_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="extended_by_id", referencedColumnName="id")}
     *      )
     */
    private $extends;
    /**
     * @ORM\ManyToMany(targetEntity="Trailer", mappedBy="requires", fetch="LAZY")
     */
    private $required_by;
    /**
     * @ORM\ManyToMany(targetEntity="Trailer", inversedBy="required_by", fetch="LAZY")
     * @ORM\JoinTable(name="trailer_require",
     *      joinColumns={@ORM\JoinColumn(name="requires_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="required_by_id", referencedColumnName="id")}
     *      )
     */
    private $requires;
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
        $this->extends = new ArrayCollection();
        $this->requires = new ArrayCollection();
        $this->extended_by = new ArrayCollection();
        $this->required_by = new ArrayCollection();
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
     * Set model
     *
     * @param int $model
     *
     * @return Asset
     */
    public function setModel( $model )
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return int
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Asset
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
     * Set serial_number
     *
     * @param string $serial_number
     *
     * @return Asset
     */
    public function setSerialNumber( $serial_number )
    {
        $this->serial_number = $serial_number;

        return $this;
    }

    /**
     * Get serial_number
     *
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serial_number;
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
     * Set purchased
     *
     * @param int $purchased
     *
     * @return Asset
     */
    public function setPurchased( $purchased )
    {
        $this->purchased = new \DateTime($purchased);

        return $this;
    }

    /**
     * Get purchased
     *
     * @return int
     */
    public function getPurchased()
    {
        return $this->purchased;
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
     * Set value
     *
     * @param float $value
     *
     * @return Asset
     */
    public function setValue( $value )
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set location
     *
     * @param int $location
     *
     * @return Asset
     */
    public function setLocation( $location )
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set LocationText
     *
     * @param string $location_text
     *
     * @return Asset
     */
    public function setLocationText( $location_text )
    {
        $this->location_text = $location_text;

        return $this;
    }

    /**
     * Get LocationText
     *
     * @return string
     */
    public function getLocationText()
    {
        return $this->location_text;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Asset
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

    public function setActive( $active )
    {
        $this->active = $active;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function getRelationships( $relationship, $full )
    {
        $relationships = [];
        if( count( $this->{$relationship} ) > 0 )
        {
            if( $full === false )
            {
                foreach( $this->{$relationship} as $r )
                {
                    $relationships[] = ['id' => $r->getId(),
                        'name' => $r->getName()];
                }
            }
            else
            {
                foreach( $this->{$relationship} as $r )
                {
                    $relationships[] = $r;
                }
            }
        }
        return $relationships;
    }

    public function setExtends( $models )
    {
        foreach( $models as $m )
        {
            $this->addExtends( $m );
        }
        return $this;
    }

    public function getExtends( $full = true )
    {
        return $this->getRelationships( 'extends', $full );
    }

    public function addExtend( Model $model )
    {
        if( !$this->extends->contains( $model ) )
        {
            $this->extends->add( $model );
        }
    }

    public function removeExtend( Model $model )
    {
        $this->extends->removeElement( $model );
    }

    public function setExtendedBy( $models )
    {
        foreach( $models as $m )
        {
            $this->addExtendedBy( $m );
        }
        return $this;
    }

    public function getExtendedBy( $full = true )
    {
        return $this->getRelationships( 'extended_by', $full );
    }

    public function addExtendedBy( Model $model )
    {
        if( !$this->extended_by->contains( $model ) )
        {
            $this->extended_by->add( $model );
        }
        return $this;
    }

    public function removeExtendedBy( Model $model )
    {
        $this->extends->removeElement( $model );
        return $this;
    }

    public function setRequires( $models )
    {
        $this->requires->clear();
        foreach( $models as $m )
        {
            $this->addRequires( $m );
        }
        return $this;
    }

    public function getRequires( $full = true )
    {
        return $this->getRelationships( 'requires', $full );
    }

    public function addRequire( Model $model )
    {
        if( !$this->requires->contains( $model ) )
        {
            $this->requires->add( $model );
        }
    }

    public function removeRequire( Model $model )
    {
        $this->requires->removeElement( $model );
    }

    public function setRequiredBy( $models )
    {
        foreach( $models as $m )
        {
            $this->addRequiredBy( $m );
        }
        return $this;
    }

    public function getRequiredBy( $full = true )
    {
        return $this->getRelationships( 'required_by', $full );
    }

    public function addRequiredBy( Model $model )
    {
        if( !$this->required_by->contains( $model ) )
        {
            $this->required_by->add( $model );
        }
    }

    public function removeRequiredBy( Model $model )
    {
        $this->requires->removeElement( $model );
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
