<?php

Namespace App\Entity\Venue;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Common\Person;
use App\Entity\Common\Address;
use App\Entity\Traits\Versioned\Active;
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Versioned\Name;
use App\Entity\Traits\History;

/**
 * Venue
 *
 * @ORM\Table(name="venue")
 * @ORM\Entity(repositoryClass="Repository\VenueRepository")
 * @Gedmo\Loggable(logEntryClass="Entity\Venue\VenueLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Venue
{

    use Active,
        Comment,
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
     * @ORM\ManyToOne(targetEntity="Entity\Common\Address", cascade={"persist"})
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $address;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    private $directions;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    private $parking;
    /**
     * @ORM\ManyToMany(targetEntity="Entity\Common\Person", cascade={"persist"})
     * @ORM\JoinTable(name="venue_contact",
     *      joinColumns={@ORM\JoinColumn(name="venue_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $contacts = null;

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

    /**
     * Set directions
     *
     * @param string $directions
     *
     * @return Venue
     */
    public function setDirections( $directions )
    {
        $this->directions = $directions;

        return $this;
    }

    /**
     * Get directions
     *
     * @return string
     */
    public function getDirections()
    {
        return $this->directions;
    }

    /**
     * Set parking
     *
     * @param string $parking
     *
     * @return Venue
     */
    public function setParking( $parking )
    {
        $this->parking = $parking;

        return $this;
    }

    /**
     * Get parking
     *
     * @return string
     */
    public function getParking()
    {
        return $this->parking;
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

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
