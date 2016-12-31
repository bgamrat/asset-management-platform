<?php

namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Brand
 *
 * @ORM\Table(name="brand")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BrandRepository")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * 
 */
class Brand
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
     * @ORM\Column(name="name", type="string", length=64, nullable=true, unique=false)
     */
    private $name;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $comment;
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     * 
     */
    private $active = true;
    /**
     * @var ArrayCollection $models
     * @ORM\OneToMany(targetEntity="Model", mappedBy="brand", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="brand_model",
     *      joinColumns={@ORM\JoinColumn(name="brand_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="model_id", referencedColumnName="id", unique=true, nullable=false)}
     *      )
     */
    protected $models = null;
    /**
     * @var ArrayCollection $vendors
     * @ORM\ManyToMany(targetEntity="Vendor", fetch="LAZY")
     * @ORM\JoinTable(name="vendor_brands",
     *      joinColumns={@ORM\JoinColumn(name="brand_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="vendor_id", referencedColumnName="id", nullable=false)}
     *      )
     */
    protected $vendors = null;
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
        $this->models = new ArrayCollection();
        $this->vendors = new ArrayCollection();
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
     * @return Brand
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
     * @return Email
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

    public function getModels()
    {
        return $this->models->toArray();
    }

    public function addModel( Model $model )
    {
        if( !$this->models->contains( $model ) )
        {
            if( $model->getBrand() !== $this )
            {
                $model->setBrand = $this;
            }
            $this->models->add( $model );
        }
    }

    public function removeModel( Model $model )
    {
        $this->models->removeElement( $model );
    }

    public function getVendors()
    {
        return $this->vendors->toArray();
    }

    public function addVendor( Vendor $vendor )
    {
        if( !$this->vendors->contains( $vendor ) )
        {
            $this->vendors->add( $vendor );
            if( !$vendor->getBrands()->contains( $this ) )
            {
                $vendor->addBrand( $this );
            }
        }
    }

    public function removeVendor( Vendor $vendor )
    {
        $vendor->removeBrand( $this );
        $this->vendors->removeElement( $vendor );
    }

    public function setActive( $active )
    {
        $this->active = $active;
    }

    public function isActive()
    {
        return $this->active;
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
