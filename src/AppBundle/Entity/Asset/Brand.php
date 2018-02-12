<?php

namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Traits\Versioned\Active;
use AppBundle\Entity\Traits\Versioned\Comment;
use AppBundle\Entity\Traits\Versioned\CustomAttributes;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\Versioned\Name;
use AppBundle\Entity\Traits\History;

/**
 * Brand
 *
 * @ORM\Table(name="brand")
 * @ORM\Entity()
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Brand
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
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Manufacturer")
     */
    private $manufacturer;
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
    public function __construct()
    {
        $this->models = new ArrayCollection();
        $this->vendors = new ArrayCollection();
    }

    /**
     * Set manufacturer
     *
     * @param Manufacturer $manufacturer
     *
     * @return Brand
     */
    public function setManufacturer( $manufacturer )
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return Manufacturer
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
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
                $model->setBrand( $this );
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

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
