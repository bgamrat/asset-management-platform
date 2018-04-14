<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Entity\Traits\Id;

/**
 * Location
 *
 * A location is a place where an item can be
 *
 * @ORM\Table(name="location")
 * @ORM\Entity()
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 */
class Location
{

    use Id,
        TimestampableEntity,
        SoftDeleteableEntity;

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
     * @ORM\ManyToOne(targetEntity="LocationType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     * @ORM\OrderBy({"type" = "ASC"})
     */
    protected $type;
    /**
     * @ORM\Column(type="integer", name="entity_id", nullable=true)
     */
    private $entity = null;
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     */
    private $address_id = null;
    /**
     * @ORM\OneToMany(targetEntity="Asset", mappedBy="location")
     */
    private $assets;
    private $entityData;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
    }

    /**
     * Set entity
     *
     * @param int $entity_id
     *
     * @return Location
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

    public function setAddressId( $address_id )
    {
        $this->address_id = $address_id;
        return $this;
    }

    public function getAddressId()
    {
        return $this->address_id;
    }

    public function isAddress()
    {
        return $this->address_id !== null;
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
     * Get assets
     *
     * @return ArrayCollection
     */
    public function getAssets()
    {
        return $this->assets->toArray();
    }

    public function setAssets( $assets )
    {
        foreach( $assets as $a )
        {
            $this->addAssets( $a );
            $a->setLocation( $this );
        }
        return $this;
    }

    public function addAsset( Model $asset )
    {
        if( !$this->extends->contains( $asset ) )
        {
            $this->extends->add( $asset );
            $a->setLocation( $this );
        }
    }

    /**
     * Set entityData
     *
     * @param object $entityData
     *
     * @return Location
     */
    public function setEntityData( $entityData )
    {
        $this->entityData = $entityData;

        return $this;
    }

    /**
     * Get entityData
     *
     * @return mixed
     */
    public function getEntityData()
    {
        return $this->entityData;
    }

}
