<?php

namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Common\Person;
use AppBundle\Entity\Asset\Brand;
use AppBundle\Entity\Traits\Versioned\Active;
use AppBundle\Entity\Traits\Versioned\Comment;
use AppBundle\Entity\Traits\Versioned\Name;

/**
 * Manufacturer
 *
 * @ORM\Table(name="manufacturer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ManufacturerRepository")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Manufacturer
{

    use Active,
        Comment,
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Common\Person", cascade={"persist"})
     * @ORM\JoinTable(name="manufacturer_contact",
     *      joinColumns={@ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $contacts = null;
    /**
     * @var ArrayCollection $brands
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Asset\Brand", cascade={"persist"})
     * @ORM\JoinTable(name="manufacturer_brand",
     *      joinColumns={@ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="brand_id", referencedColumnName="id", onDelete="CASCADE", unique=true, nullable=false)}
     *      )
     */
    protected $brands = null;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->brands = new ArrayCollection();
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

    public function getBrands( $deep = true )
    {
        $return = [];
        if( $deep === true )
        {
            $return = $this->brands->toArray();
        }
        else
        {
            foreach( $this->brands as $b )
            {
                $br = [];
                $br['id'] = $b->getId();
                $br['name'] = $b->getName();
                $br['comment'] = $b->getComment();
                $br['active'] = $b->isActive();
                $return[] = $br;
            }
        }
        return $return;
    }

    public function addBrand( Brand $brand )
    {
        if( !$this->brands->contains( $brand ) )
        {
            $this->brands->add( $brand );
            $brand->setManufacturer( $this );
        }
        return $this;
    }

    public function removeBrand( Brand $brand )
    {
        $this->brands->removeElement( $brand );
    }

    public function getContacts()
    {
        $return = $this->contacts->toArray();
        return $return;
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

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
