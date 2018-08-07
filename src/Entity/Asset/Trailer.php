<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Entity\Traits\Versioned\Active;
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Versioned\Cost;
use App\Entity\Traits\Id;
use App\Entity\Traits\Versioned\Name;
use App\Entity\Traits\Versioned\Value;
use App\Entity\Traits\History;

/**
 * Asset
 *
 * @ORM\Table(name="trailer")
 * @ORM\Entity()
 * @Gedmo\Loggable(logEntryClass="App\Entity\Asset\TrailerLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * 
 */
class Trailer
{

    use Active,
        Comment,
        Cost,
        Id,
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
     * @var int
     * @Gedmo\Versioned
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="Model", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id")
     */
    protected $model = null;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="serial_number", type="string", length=64, nullable=true, unique=false)
     */
    protected $serial_number;
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
     * @var int
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="assets", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    protected $location = null;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="location_text", type="text", nullable=true, unique=false)
     */
    protected $location_text = null;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $description;
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

    public function __construct()
    {
        $this->extends = new ArrayCollection();
        $this->requires = new ArrayCollection();
        $this->extended_by = new ArrayCollection();
        $this->required_by = new ArrayCollection();
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
        if( !empty( $purchased ) )
        {
            $this->purchased = $purchased;
        }
        else
        {
            $this->purchased = null;
        }

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
     * s
     * @return Asset
     */
    public function setLocationText( $location_text )
    {
        $this->location_text = preg_replace( '/\n+/', PHP_EOL, str_replace( ['<br />', '<br>'], PHP_EOL, $location_text ) );

        return $this;
    }

    /**
     * Get LocationText
     *
     * @return string
     */
    public function getLocationText()
    {
        return str_replace( PHP_EOL, '<br>', $this->location_text );
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Trailer
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

    public function setExtends( $trailers )
    {
        foreach( $trailers as $m )
        {
            $this->addExtends( $m );
        }
        return $this;
    }

    public function getExtends( $full = true )
    {
        return $this->getRelationships( 'extends', $full );
    }

    public function addExtend( Trailer $trailer )
    {
        if( !$this->extends->contains( $trailer ) )
        {
            $this->extends->add( $trailer );
        }
    }

    public function removeExtend( Trailer $trailer )
    {
        $this->extends->removeElement( $trailer );
    }

    public function setExtendedBy( $trailers )
    {
        foreach( $trailers as $m )
        {
            $this->addExtendedBy( $m );
        }
        return $this;
    }

    public function getExtendedBy( $full = true )
    {
        return $this->getRelationships( 'extended_by', $full );
    }

    public function addExtendedBy( Trailer $trailer )
    {
        if( !$this->extended_by->contains( $trailer ) )
        {
            $this->extended_by->add( $trailer );
        }
        return $this;
    }

    public function removeExtendedBy( Trailer $trailer )
    {
        $this->extends->removeElement( $trailer );
        return $this;
    }

    public function setRequires( $trailers )
    {
        $this->requires->clear();
        foreach( $trailers as $m )
        {
            $this->addRequires( $m );
        }
        return $this;
    }

    public function getRequires( $full = true )
    {
        return $this->getRelationships( 'requires', $full );
    }

    public function addRequire( Trailer $trailer )
    {
        if( !$this->requires->contains( $trailer ) )
        {
            $this->requires->add( $trailer );
        }
    }

    public function removeRequire( Trailer $trailer )
    {
        $this->requires->removeElement( $trailer );
    }

    public function setRequiredBy( $trailers )
    {
        foreach( $trailers as $m )
        {
            $this->addRequiredBy( $m );
        }
        return $this;
    }

    public function getRequiredBy( $full = true )
    {
        return $this->getRelationships( 'required_by', $full );
    }

    public function addRequiredBy( Trailer $trailer )
    {
        if( !$this->required_by->contains( $trailer ) )
        {
            $this->required_by->add( $trailer );
        }
    }

    public function removeRequiredBy( Trailer $trailer )
    {
        $this->requires->removeElement( $trailer );
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

    public function getHistory()
    {
        return $this->history;
    }

    public function setHistory( $history )
    {
        $this->history = $history;
    }

}
