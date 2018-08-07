<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Common\Person;
use App\Entity\Asset\Brand;
use App\Entity\Traits\Versioned\Active;
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Versioned\Name;
use App\Entity\Traits\Id;

/**
 * Manufacturer
 *
 * @ORM\Table(name="manufacturer")
 * @ORM\Entity(repositoryClass="App\Repository\ManufacturerRepository")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Manufacturer
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Common\Person", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="manufacturer_contact",
     *      joinColumns={@ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")}
     *      )
     */
    private $contacts = null;
    /**
     * @var ArrayCollection $brands
     * @ORM\ManyToMany(targetEntity="App\Entity\Asset\Brand", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="manufacturer_brand",
     *      joinColumns={@ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="brand_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)}
     *      )
     */
    protected $brands = null;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->brands = new ArrayCollection();
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
