<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Entity\Common\Person;
use Entity\Traits\Versioned\Active;
use Entity\Traits\Versioned\Comment;
use Entity\Traits\Id;
use Entity\Traits\Versioned\Name;

/**
 * Vendor
 *
 * @ORM\Table(name="vendor")
 * @ORM\Entity(repositoryClass="Repository\VendorRepository")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Vendor
{

    use Active,
        Comment,
        Id,
        Name,
        TimestampableEntity,
        SoftDeleteableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToMany(targetEntity="Entity\Common\Person", cascade={"persist"})
     * @ORM\JoinTable(name="vendor_contact",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="vendor_id", referencedColumnName="id")}
     *      )
     */
    private $contacts = null;
    /**
     * @var ArrayCollection $brands
     * @ORM\ManyToMany(targetEntity="Brand")
     * @ORM\JoinTable(name="vendor_brand",
     *      joinColumns={@ORM\JoinColumn(name="brand_id", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="vendor_id", referencedColumnName="id", nullable=false)}
     *      )
     */
    protected $brands = null;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="rma_required", type="boolean") 
     */
    private $rmaRequired = false;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $serviceInstructions = null;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->brands = new ArrayCollection();
    }

    public function getBrandData()
    {
        $data = [];
        foreach( $this->brands as $b )
        {
            $data[] = ['id' => $b->getId(), 'name' => $b->getName()];
        }
        return $data;
    }

    public function getBrands()
    {
        return $this->brands->toArray();
    }

    public function addBrand( Brand $brand )
    {
        if( !$this->brands->contains( $brand ) )
        {
            $this->brands->add( $brand );
        }
    }

    public function removeBrand( Brand $brand )
    {
        $this->brands->removeElement( $brand );
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

    public function setRmaRequired( $rmaRequired )
    {
        $this->rmaRequired = $rmaRequired;
    }

    public function isRmaRequired()
    {
        return $this->rmaRequired;
    }

    /**
     * Set ServiceInstructions
     *
     * @param string $serviceInstructions
     *
     * @return Vendor
     */
    public function setServiceInstructions( $serviceInstructions )
    {
        $this->serviceInstructions = $serviceInstructions;

        return $this;
    }

    /**
     * Get ServiceInstructions
     *
     * @return string
     */
    public function getServiceInstructions()
    {
        return $this->serviceInstructions;
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
