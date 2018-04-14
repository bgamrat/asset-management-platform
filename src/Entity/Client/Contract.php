<?php

Namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Entity\Client\Trailer;
use Entity\Common\CategoryQuantity;
use Entity\Traits\Versioned\Active;
use Entity\Traits\Versioned\Comment;
use Entity\Traits\Versioned\DateSpan;
use Entity\Traits\Id;
use Entity\Traits\Versioned\Name;
use Entity\Traits\Versioned\Value;

/**
 * Contract
 *
 * @ORM\Table(name="contract")
 * @ORM\Entity(repositoryClass="Repository\ContractRepository")
 * @Gedmo\Loggable(logEntryClass="Entity\Client\ContractLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Contract
{

    use Active,
        Comment,
        DateSpan,
        Id,
        Name,
        Value,
        TimestampableEntity,
        SoftDeleteableEntity;

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
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="container", type="boolean")
     */
    private $container = false;
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
     * @ORM\ManyToMany(targetEntity="Entity\Common\CategoryQuantity", cascade={"persist"})
     * @ORM\JoinTable(name="contract_category_quantity_required",
     *      joinColumns={@ORM\JoinColumn(name="requires_id", referencedColumnName="id")}
     *      )
     */
    private $requiresCategoryQuantities;
    /**
     * @ORM\ManyToMany(targetEntity="Entity\Common\CategoryQuantity", cascade={"persist"})
     * @ORM\JoinTable(name="contract_category_quantity_available",
     *      joinColumns={@ORM\JoinColumn(name="available_id", referencedColumnName="id")}
     *      )
     */
    private $availableCategoryQuantities;

    public function __construct()
    {
        $this->requiresTrailers = new ArrayCollection();
        $this->availableTrailers = new ArrayCollection();
        $this->requiresCategoryQuantities = new ArrayCollection();
        $this->availableCategoryQuantities = new ArrayCollection();
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

    public function setContainer( $container )
    {
        $this->container = $container;
        return $this;
    }

    public function isContainer()
    {
        return $this->container;
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

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
