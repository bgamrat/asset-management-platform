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
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="container", type="boolean")
     */
    private $container = false;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Gedmo\Versioned
     */
    private $comment;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="value", type="float", nullable=true, unique=false)
     */
    private $value = 0.0;
    /**
     * @ORM\ManyToMany(targetEntity="CategoryQuantity")
     * @ORM\JoinTable(name="contract_categoryquantity_required",
     *      joinColumns={@ORM\JoinColumn(name="requires_id", referencedColumnName="id")}
     *      )
     */
    private $requires;
    /**
     * @ORM\ManyToMany(targetEntity="CategoryQuantity")
     * @ORM\JoinTable(name="contract_categoryquantity_available",
     *      joinColumns={@ORM\JoinColumn(name="available_id", referencedColumnName="id")}
     *      )
     */
    private $available;
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
        $this->requires = new ArrayCollection();
        $this->available = new ArrayCollection();
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

    public function setContainer( $container )
    {
        $this->container = $container;
    }

    public function isContainer()
    {
        return $this->container;
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

    public function setRequires( $categoryQuantities )
    {
        $this->requires->clear();
        foreach( $categoryQuantities as $m )
        {
            $this->addRequires( $m );
        }
        return $this;
    }

    public function getRequires( $full = true )
    {
        return $this->getCategoryQuantities( 'requires', $full );
    }

    public function addRequire( CategoryQuantity $categoryQuantity )
    {
        if( !$this->requires->contains( $categoryQuantity ) )
        {
            $this->requires->add( $categoryQuantity );
        }
    }

    public function removeRequire( CategoryQuantity $categoryQuantity )
    {
        $this->requires->removeElement( $categoryQuantity );
    }

    public function setAvailable( $categoryQuantities )
    {
        foreach( $categoryQuantities as $m )
        {
            $this->addAvailable( $m );
        }
        return $this;
    }

    public function getAvailable( $full = true )
    {
        return $this->getCategoryQuantities( 'available', $full );
    }

    public function addAvailable( CategoryQuantity $categoryQuantity )
    {
        if( !$this->available->contains( $categoryQuantity ) )
        {
            $this->available->add( $categoryQuantity );
        }
    }

    public function removeAvailable( CategoryQuantity $categoryQuantity )
    {
        $this->requires->removeElement( $categoryQuantity );
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
