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
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Versioned\Name;
use App\Entity\Traits\Id;
use App\Entity\Asset\Asset;

/**
 * IssueItem
 *
 * @ORM\Entity()
 * @ORM\Table(name="issue_item")
 * @Gedmo\Loggable(logEntryClass="Entity\Asset\IssueLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class IssueItem
{

    use Id,
        Name,
        Comment,
        TimestampableEntity,
        SoftDeleteableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="items")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="\Entity\Asset\Asset")
     * @ORM\JoinColumn(name="asset_id", referencedColumnName="id")
     */
    private $asset = null;

    /**
     * Set asset
     *
     * @param string $asset
     *
     * @return IssueItem
     */
    public function setAsset( Asset $asset )
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

}
