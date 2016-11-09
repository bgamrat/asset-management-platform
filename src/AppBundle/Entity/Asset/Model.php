<?php

namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model
 *
 * @ORM\Table(name="model")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModelRepository")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Asset\ModelLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(
 *     fields={"brand", "name"},
 *     message="name.must-be-unique")
 */
class Model
{

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
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $category;
    /**
     * @var string
     * @Assert\NotBlank(
     *     message = "blank.name")
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9x\.\,\ \+\(\)-]{2,32}$/",
     *     htmlPattern = "^[a-zA-Z0-9x\.\,\ \+\(\)-]{2,32}$",
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
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="models")
     * @Gedmo\Versioned
     */
    private $brand;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;
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
     * Set id
     * 
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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Model
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
     * @return Comment
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
        if( count($this->{$relationship}) > 0 )
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
    
    public function getUpdated() {
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

    public function getBrandModelName()
    {
        return $this->getBrand()->getName() . ' ' . $this->getName();
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'category' => $this->getCategory(),
            'name' => $this->getName(),
            'comment' => $this->getComment(),
            'active' => $this->isActive(),
            'extends' => $this->getExtends( false ),
            'extended_by' => $this->getExtendedBy( false ),
            'requires' => $this->getRequires( false ),
            'required_by' => $this->getRequiredBy( false )
        ];
    }
}
