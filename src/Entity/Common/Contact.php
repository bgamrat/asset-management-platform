<?php

Namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Traits\Versioned\Active;
use App\Entity\Traits\Id;
use App\Entity\Traits\Versioned\Name;

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

    use Active,
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

    public function __construct()
    {
        $this->assets = new ArrayCollection();
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

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

    public function getLabel()
    {
        return (!empty( $this->address ) ? nl2br( $this->name . PHP_EOL . $this->address->getAddress() ) : $this->name);
    }

    public function getHash()
    {
        $entityType = $this->getType()->getEntity();
        $entityId = $this->getEntity();
        $personId = $this->getPerson()->getId();
        $addressId = ($this->getAddress() !== null) ? $this->getAddress()->getId() : null;
        return ($entityType . '/' . $entityId . '/' . $personId . '/' . $addressId);
    }

}
