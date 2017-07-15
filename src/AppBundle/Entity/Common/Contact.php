<?php

namespace AppBundle\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Contact
 *
 * Contacts support BillTos by associating a specific address for a person with an
 * entity such as a client or venue
 *
 * @ORM\Table(name="contact") * 
 * @ORM\Entity()
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * 
 */
class Contact
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Person", mappedBy="id")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="ContactType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     * @ORM\OrderBy({"type" = "ASC"})
     */
    protected $type;
    /**
     * @ORM\Column(type="integer", name="entity_id", nullable=true)
     */
    private $entity = null;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false, unique=false)
     * @Gedmo\Versioned
     */
    private $name;
    /**
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     * @ORM\OrderBy({"type" = "ASC"})
     */
    protected $person;
    /**
     * @ORM\ManyToOne(targetEntity="Address", cascade={"persist"})
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $address;
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     * 
     */
    private $active = true;
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
        $this->assets = new ArrayCollection();
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
     * Set type
     *
     * @param int $type
     *
     * @return Contact
     */
    public function setType( $type )
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set person
     *
     * @param int $person
     *
     * @return Contact
     */
    public function setPerson( $person )
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return int
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set entity
     *
     * @param int $entity_id
     *
     * @return Contact
     */
    public function setEntity( $entity )
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return int
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Contact
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
     * Set address
     *
     * @param int $address
     *
     * @return Contact
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

    public function setActive( $active )
    {
        $this->active = $active;
    }

    public function isActive()
    {
        return $this->active;
    }

    // Must match the one in Person
    public function getHash()
    {
        $entityType = $this->getType()->getEntity();
        $entityId = $this->getEntity();
        $personId = $this->getPerson()->getId();
        $addressId = ($this->getAddress() !== null) ? $this->getAddress()->getId() : null;
        return ($entityType . '/' . $entityId . '/' . $personId . '/' . $addressId);
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
