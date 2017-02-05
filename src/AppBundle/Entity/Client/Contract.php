<?php

namespace AppBundle\Entity\Client;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contract
 *
 * @ORM\Table(name="contract")
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Client\ContractLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Contract
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Client", mappedBy="id")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $client;
    /**
     * @var string
     * @Assert\NotBlank(
     *     message = "blank.name")
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9x\.\,\ \+\(\)\'\x22-]{2,32}$/",
     *     htmlPattern = "^[a-zA-Z0-9x\.\,\ \+\(\)\'\x22-]{2,32}$",
     *     message = "invalid.name {{ value }}",
     *     match=true)
     * @ORM\Column(name="name", type="string", length=64, nullable=true, unique=false)
     * @Gedmo\Versioned
     */
    private $name;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Gedmo\Versioned
     */
    private $comment;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="container", type="boolean")
     */
    private $container = false;
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
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="value", type="float", nullable=true, unique=false)
     */
    private $value = 0.0;
    /**
     * @ORM\ManyToMany(targetEntity="Trailer", cascade={"persist"})
     * @ORM\JoinTable(name="contract_trailer_required",
     *      joinColumns={@ORM\JoinColumn(name="requires_id", referencedColumnName="id")}
     *      )
     */
    private $requiresTrailers;
    /**
     * @ORM\ManyToMany(targetEntity="Trailer", cascade={"persist"})
     * @ORM\JoinTable(name="contract_trailer_available",
     *      joinColumns={@ORM\JoinColumn(name="available_id", referencedColumnName="id")}
     *      )
     */
    private $availableTrailers;
    /**
     * @ORM\ManyToMany(targetEntity="CategoryQuantity", cascade={"persist"})
     * @ORM\JoinTable(name="contract_category_quantity_required",
     *      joinColumns={@ORM\JoinColumn(name="requires_id", referencedColumnName="id")}
     *      )
     */
    private $requiresCategoryQuantities;
    /**
     * @ORM\ManyToMany(targetEntity="CategoryQuantity", cascade={"persist"})
     * @ORM\JoinTable(name="contract_category_quantity_available",
     *      joinColumns={@ORM\JoinColumn(name="available_id", referencedColumnName="id")}
     *      )
     */
    private $availableCategoryQuantities;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="active", type="boolean")
     * 
     */
    private $active = true;
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
        $this->requiresTrailers = new ArrayCollection();
        $this->availableTrailers = new ArrayCollection();
        $this->requiresCategoryQuantities = new ArrayCollection();
        $this->availableCategoryQuantities = new ArrayCollection();
    }

    /**
     * Set id
     * 
     * @return Contract
     */
    public function setId( $id )
    {
        $this->id = $id;

        return $this;
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
     * Set client
     *
     * @param string $client
     *
     * @return Contract
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
     * Set name
     *
     * @param string $name
     *
     * @return Contract
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
     * Set comment
     *
     * @param string $comment
     *
     * @return Contract
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

    public function setContainer( $container )
    {
        $this->container = $container;
        return $this;
    }

    public function isContainer()
    {
        return $this->container;
    }

    /**
     * Set start
     *
     * @param float $start
     *
     * @return Contract
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
     * @return Contract
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

    /**
     * Set value
     *
     * @param float $value
     *
     * @return Contract
     */
    public function setValue( $value )
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    public function setActive( $active )
    {
        $this->active = $active;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function getTrailers( $trailer, $full )
    {
        $trailers = [];
        if( count( $this->{$trailer} ) > 0 )
        {
            if( $full === false )
            {
                foreach( $this->{$trailer} as $t )
                {
                    $tr = $t->getTrailer();
                    $trailers[] = ['id' => $tr->getId(),
                        'name' => $tr->getName()];
                }
            }
            else
            {
                foreach( $this->{$trailer} as $tr )
                {
                    $trailers[] = $tr;
                }
            }
        }
        return $trailers;
    }

    public function setRequiresTrailers( $trailers )
    {
        $this->requiresTrailers->clear();
        foreach( $trailers as $m )
        {
            $this->addRequiresTrailers( $m );
        }
        return $this;
    }

    public function getRequiresTrailers( $full = true )
    {
        return $this->getTrailers( 'requiresTrailers', $full );
    }

    public function addRequiresTrailers( Trailer $trailer )
    {
        if( !$this->requiresTrailers->contains( $trailer ) )
        {
            $this->requiresTrailers->add( $trailer );
        }
    }

    public function removeRequiresTrailers( Trailer $trailer )
    {
        $this->requiresTrailers->removeElement( $trailer );
    }

    public function setAvailableTrailers( $trailers )
    {
        foreach( $trailers as $m )
        {
            $this->addAvailableTrailer( $m );
        }
        return $this;
    }

    public function getAvailableTrailers( $full = true )
    {
        return $this->getTrailers( 'availableTrailers', $full );
    }

    public function addAvailableTrailers( Trailer $trailer )
    {
        if( !$this->availableTrailers->contains( $trailer ) )
        {
            $this->availableTrailers->add( $trailer );
        }
    }

    public function removeAvailableTrailers( Trailer $trailer )
    {
        $this->availableTrailers->removeElement( $trailer );
    }

    public function getCategoryQuantities( $categoryQuantity, $full )
    {
        $categoryQuantities = [];
        if( count( $this->{$categoryQuantity} ) > 0 )
        {
            if( $full === false )
            {
                foreach( $this->{$categoryQuantity} as $cq )
                {
                    $categoryQuantities[] = ['id' => $cq->getId(),
                        'category' => $cq->getName(),
                        'quantity' => $cq->getQuantity()];
                }
            }
            else
            {
                foreach( $this->{$categoryQuantity} as $cq )
                {
                    $categoryQuantities[] = $cq;
                }
            }
        }
        return $categoryQuantities;
    }

    public function setRequiresCategoryQuantities( $categoryQuantities )
    {
        $this->requiresCategoryQuantities->clear();
        foreach( $categoryQuantities as $m )
        {
            $this->addRequiresCategoryQuantities( $m );
        }
        return $this;
    }

    public function getRequiresCategoryQuantities( $full = true )
    {
        return $this->getCategoryQuantities( 'requiresCategoryQuantities', $full );
    }

    public function addRequiresCategoryQuantity( CategoryQuantity $categoryQuantity )
    {
        if( !$this->requiresCategoryQuantities->contains( $categoryQuantity ) )
        {
            $this->requiresCategoryQuantities->add( $categoryQuantity );
        }
    }

    public function removeRequiresCategoryQuantity( CategoryQuantity $categoryQuantity )
    {
        $this->requiresCategoryQuantities->removeElement( $categoryQuantity );
    }

    public function setAvailableCategoryQuantities( $categoryQuantities )
    {
        foreach( $categoryQuantities as $m )
        {
            $this->addAvailableCategoryQuantity( $m );
        }
        return $this;
    }

    public function getAvailableCategoryQuantities( $full = true )
    {
        return $this->getCategoryQuantities( 'availableCategoryQuantities', $full );
    }

    public function addAvailableCategoryQuantity( CategoryQuantity $categoryQuantity )
    {
        if( !$this->availableCategoryQuantities->contains( $categoryQuantity ) )
        {
            $this->availableCategoryQuantities->add( $categoryQuantity );
        }
    }

    public function removeAvailableCategoryQuantity( CategoryQuantity $categoryQuantity )
    {
        $this->availableCategoryQuantities->removeElement( $categoryQuantity );
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
