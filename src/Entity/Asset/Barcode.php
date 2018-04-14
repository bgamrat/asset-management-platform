<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Entity\Traits\Versioned\Active;
use Entity\Traits\Versioned\Comment;
use Entity\Traits\Id;

/**
 * Barcode
 *
 * @ORM\Table(name="barcode")
 * @ORM\Entity()
 * @Gedmo\Loggable(logEntryClass="Entity\Asset\BarcodeLog")
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
     * @ORM\ManyToMany(targetEntity="Asset", mappedBy="barcodes", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $barcode;

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

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
