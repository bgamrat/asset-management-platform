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
use Entity\Traits\Versioned\Comment;
use Entity\Traits\Id;

/**
 * TransferItem
 *
 * @ORM\Table(name="transfer_item")
 * @Gedmo\Loggable(logEntryClass="Entity\Asset\TransferLog")
 * @ORM\Entity()
 * 
 */
class TransferItem
{

    use Comment,
        Id,
        SoftDeleteableEntity,
        TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\ManyToOne(targetEntity="Transfer", inversedBy="items")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="\Entity\Asset\Asset")
     * @ORM\JoinColumn(name="asset_id", referencedColumnName="id")
     */
    private $asset = null;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $rma;

    /**
     * Set asset
     *
     * @param string $asset
     *
     * @return TransferItem
     */
    public function setAsset( $asset )
    {
        $this->asset = $asset;

        $name = '';
        if( !empty( $asset ) )
        {
            $barcodes = $asset->getBarcodes();
            if( !empty( $barcodes ) )
            {
                foreach( $barcodes as $b )
                {
                    if( $b->isActive() )
                    {
                        $name = $b->getBarcode() . ' - ';
                    }
                }
            }
            $model = $asset->getModel();
            $name .= $model->getBrand()->getName() . ' ' . $asset->getModel()->getName();
        }
        $this->name = $name;

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

    /**
     * Set rma
     *
     * @param string $rma
     *
     * @return TransferItem
     */
    public function setRma( $rma )
    {
        $this->rma = $rma;

        return $this;
    }

    /**
     * Get rma
     *
     * @return string
     */
    public function getRma()
    {
        return $this->rma;
    }

}
