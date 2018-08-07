<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\Traits\Versioned\Active;
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Id;

/**
 * Barcode
 *
 * @ORM\Table(name="barcode")
 * @ORM\Entity()
 * @Gedmo\Loggable(logEntryClass="App\Entity\Asset\BarcodeLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity("barcode")
 */
class Barcode
{

    use Id,
        Active,
        Comment,
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
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $barcode;

    /**
     * @ORM\ManyToOne(targetEntity="Asset", inversedBy="barcodes", fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="asset_id", referencedColumnName="id")
     */
    private $asset = null;

    /**
     * Set barcode
     *
     * @param string $barcode
     *
     * @return Barcode
     */
    public function setBarcode( $barcode )
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * Get barcode
     *
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * Set asset
     *
     * @param string $asset
     *
     * @return Asset
     */
    public function setAsset( Asset $asset )
    {
        $this->asset = $asset;
        return $this;
    }

    /**
     * Get asset
     *
     * @return string
     */
    public function getAsset()
    {
        return $this->asset;
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
