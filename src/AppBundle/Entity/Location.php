<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Location
 *
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LocationRepository")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * 
 */
class Location
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
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="LocationType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * @ORM\OrderBy({"type" = "ASC"})
     */
    private $type;
    /**
     * @ORM\Column(type="integer", name="location_id")
     */
    private $locationId;
    /**
     * @ORM\OneToMany(targetEntity="Asset", mappedBy="location")
     */
    private $assets;
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     * 
     */
    private $active = true;
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set locationid
     *
     * @param int $location_id
     *
     * @return Location
     */
    public function setLocationId( $locationId )
    {
        $this->locationId = $locationId;

        return $this;
    }

    /**
     * Get locationId
     *
     * @return int
     */
    public function getLocationId()
    {
        return $this->locationId;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return Location
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
    /**
     * Get assets
     *
     * @return ArrayCollection
     */
    public function getAssets()
    {
        return $this->assets->toArray();
    }
    public function setActive( $active )
    {
        $this->active = $active;
    }

    public function isActive()
    {
        return $this->active;
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

    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'comment' => $this->getComment(),
            'assets' => $this->getAssets(),
            'active' => $this->isActive()
        ];
    }

}
