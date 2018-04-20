<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Entity\Traits\Versioned\Active;
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Versioned\Cost;
use App\Entity\Traits\Versioned\CustomAttributes;
use App\Entity\Traits\Id;
use App\Entity\Traits\Versioned\Name;
use App\Entity\Traits\Versioned\Value;
use App\Entity\Traits\History;

/**
 * Asset
 *
 * @ORM\Table(name="asset")
 * @ORM\Entity(repositoryClass="App\Repository\AssetRepository")
 * @Gedmo\Loggable(logEntryClass="App\Entity\Asset\AssetLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * 
 */
class Asset
{

    use Active,
        Comment,
        Cost,
        CustomAttributes,
        Id,
        Value,
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
     * @var int
     * @Gedmo\Versioned
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="Model")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id")
     */
    protected $model = null;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="serial_number", type="string", length=64, nullable=true, unique=false)
     */
    private $serial_number;
    /**
     * @var int
     * @Gedmo\Versioned
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="AssetStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    protected $status = null;
    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="purchased", type="datetime", nullable=true, unique=false)
     */
    private $purchased = null;
    /**
     * @var int
     * @Gedmo\Versioned
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="Vendor")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    protected $owner = null;
    /**
     * @var int
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="assets", cascade={"persist"})
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    protected $location = null;
    /**
     * @var text
     * @Gedmo\Versioned
     * @ORM\Column(name="location_text", type="text", nullable=true, unique=false)
     */
    protected $location_text = null;
    /**
     * @var ArrayCollection $barcodes
     * @ORM\ManyToMany(targetEntity="Barcode", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     * @ORM\JoinTable(name="asset_barcode",
     *      joinColumns={@ORM\JoinColumn(name="asset_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="barcode_id", referencedColumnName="id", unique=true, nullable=false)}
     *      )
     */
    protected $barcodes;

    public function __construct()
    {
        $this->barcodes = new ArrayCollection();
    }

    /**
     * Set model
     *
     * @param int $model
     *
     * @return Asset
     */
    public function setModel( $model )
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return int
     */
    public function getModel()
    {
        return $this->model;
    }

    public function getName()
    {
        return $this->model;
    }

    /**
     * Set serial_number
     *
     * @param string $serial_number
     *
     * @return Asset
     */
    public function setSerialNumber( $serial_number )
    {
        $this->serial_number = $serial_number;

        return $this;
    }

    /**
     * Get serial_number
     *
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serial_number;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return Asset
     */
    public function setStatus( $status )
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set purchased
     *
     * @param int $purchased
     *
     * @return Asset
     */
    public function setPurchased( $purchased )
    {
        if( !empty( $purchased ) )
        {
            $this->purchased = $purchased;
        }
        else
        {
            $this->purchased = null;
        }

        return $this;
    }

    /**
     * Get purchased
     *
     * @return int
     */
    public function getPurchased()
    {
        if( $this->purchased === null )
        {
            return $this->created;
        }
        else
        {
            return $this->purchased;
        }
    }

    /**
     * Set owner
     *
     * @param int $owner
     *
     * @return Asset
     */
    public function setOwner( $owner )
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return int
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set location
     *
     * @param int $location
     *
     * @return Asset
     */
    public function setLocation( $location )
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set LocationText
     *
     * @param string $location_text
     * s
     * @return Asset
     */
    public function setLocationText( $location_text )
    {
        $this->location_text = preg_replace( '/\n+/', PHP_EOL, str_replace( ['<br />', '<br>'], PHP_EOL, $location_text ) );

        return $this;
    }

    /**
     * Get LocationText
     *
     * @return string
     */
    public function getLocationText()
    {
        return str_replace( PHP_EOL, '<br>', $this->location_text );
    }

    public function getBarcodes()
    {
        return $this->barcodes->toArray();
    }

    public function addBarcode( Barcode $barcode )
    {
        if( !$this->barcodes->contains( $barcode ) )
        {
            $this->barcodes->add( $barcode );
        }
    }

    public function removeBarcode( Barcode $barcode )
    {
        $this->barcodes->removeElement( $barcode );
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
