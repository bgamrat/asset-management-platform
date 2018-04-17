<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Traits\Versioned\Active;
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Versioned\CustomAttributes;
use App\Entity\Traits\Id;
use App\Entity\Traits\Versioned\Name;
use App\Entity\Traits\History;

/**
 * Model
 *
 * @ORM\Table(name="model")
 * @ORM\Entity(repositoryClass="Repository\ModelRepository")
 * @Gedmo\Loggable(logEntryClass="Entity\Asset\ModelLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(
 *     fields={"brand", "name"},
 *     message="name.must-be-unique")
 */
class Model
{

    use Active,
        Comment,
        CustomAttributes,
        Id,
        Name,
        TimestampableEntity,
        SoftDeleteableEntity,
        History;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Asset", mappedBy="id")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="models")
     * @Gedmo\Versioned
     */
    private $brand;
    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $category;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="container", type="boolean", options={"default":false})
     */
    private $container;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(type="float", nullable=true, unique=false)
     */
    private $weight;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="default_contract_value", type="float", nullable=true, unique=false)
     */
    private $defaultContractValue = 0.0;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="default_event_value", type="float", nullable=true, unique=false)
     */
    private $defaultEventValue = 0.0;
    /**
     * @ORM\ManyToMany(targetEntity="Model", mappedBy="extends", fetch="LAZY")
     */
    private $extended_by;
    /**
     * @ORM\ManyToMany(targetEntity="Model", inversedBy="extended_by", fetch="LAZY")
     * @ORM\JoinTable(name="model_extend",
     *      joinColumns={@ORM\JoinColumn(name="extends_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="extended_by_id", referencedColumnName="id")}
     *      )
     */
    private $extends;
    /**
     * @ORM\ManyToMany(targetEntity="Model", mappedBy="requires", fetch="LAZY")
     */
    private $required_by;
    /**
     * @ORM\ManyToMany(targetEntity="Model", inversedBy="required_by", fetch="LAZY")
     * @ORM\JoinTable(name="model_require",
     *      joinColumns={@ORM\JoinColumn(name="requires_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="required_by_id", referencedColumnName="id")}
     *      )
     */
    private $requires;
    /**
     * @ORM\ManyToMany(targetEntity="Category", fetch="LAZY")
     * @ORM\JoinTable(name="model_satisfy",
     *      joinColumns={@ORM\JoinColumn(name="satisfy_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="model_id", referencedColumnName="id")}
     *      )
     */
    private $satisfies;
    /**
     * @var float
     * @ORM\Column(name="carnet_value", type="float", nullable=true, unique=false) 
     */
    private $carnetValue;

    public function __construct()
    {
        $this->extends = new ArrayCollection();
        $this->requires = new ArrayCollection();
        $this->extended_by = new ArrayCollection();
        $this->required_by = new ArrayCollection();
        $this->satisfies = new ArrayCollection();
    }

    /**
     * Get brand
     *
     * @return Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    public function setBrand( $brand )
    {
        $this->brand = $brand;

        $brand->addModel( $this );

        return $this;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Category
     */
    public function setCategory( $category )
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
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
     * Set weight
     *
     * @param float $weight
     *
     * @return Model
     */
    public function setWeight( $weight )
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set defaultContractValue
     *
     * @param float $defaultContractValue
     *
     * @return Model
     */
    public function setDefaultContractValue( $defaultContractValue )
    {
        $this->defaultContractValue = $defaultContractValue;

        return $this;
    }

    /**
     * Get defaultContractValue
     *
     * @return float
     */
    public function getDefaultContractValue()
    {
        return $this->defaultContractValue;
    }

    /**
     * Set defaultEventValue
     *
     * @param float $defaultEventValue
     *
     * @return Model
     */
    public function setDefaultEventValue( $defaultEventValue )
    {
        $this->defaultEventValue = $defaultEventValue;

        return $this;
    }

    /**
     * Get defaultEventValue
     *
     * @return float
     */
    public function getDefaultEventValue()
    {
        return $this->defaultEventValue;
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
                        'name' => $r->getBrandModelName()];
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

    public function setSatisfies( $categories )
    {
        foreach( $categories as $c )
        {
            $this->addSatisfies( $c );
        }
        return $this;
    }

    public function getSatisfies()
    {
        return $this->satisfies;
    }

    public function addSatisfies( Category $category )
    {
        if( !$this->satisfies->contains( $category ) )
        {
            $this->satisfies->add( $category );
        }
    }

    public function removeSatisfies( Category $category )
    {
        $this->satisfies->removeElement( $category );
    }

    public function setCarnetValue( $value )
    {
        $this->carnetValue = $value;
    }

    public function getCarnetValue()
    {
        return $this->carnetValue;
    }

    public function getBrandModelName()
    {
        return $this->getBrand()->getName() . ' ' . $this->getName();
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
