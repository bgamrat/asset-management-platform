<?php

namespace AppBundle\Entity\Venue;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Common\Person;
use AppBundle\Entity\Common\Address;

/**
 * Venue
 *
 * @ORM\Table(name="venue")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VenueRepository")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Venue
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
     * @ORM\Column(name="name", type="string", length=64, nullable=true, unique=true)
     * @Gedmo\Versioned
     */
    private $name;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Common\Address", cascade={"persist"})
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $address;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="active", type="boolean")
     * 
     */
    private $active = true;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $comment;
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Common\Person", cascade={"persist"})
     * @ORM\JoinTable(name="venue_contact",
     *      joinColumns={@ORM\JoinColumn(name="venue_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $contacts = null;
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
        $this->contacts = new ArrayCollection();
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
     * @return Venue
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

    public function setActive( $active )
    {
        $this->active = $active;
    }

    /**
     * Set address
     *
     * @param int $address
     *
     * @return Venue
     */
    public function setAddress( $address )
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return int
     */
    public function getAddress()
    {
        return $this->address;
    }

    public function isActive()
    {
        return $this->active;
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
