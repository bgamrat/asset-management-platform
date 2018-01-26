<?php

Namespace AppBundle\Entity\Schedule;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Entity\Traits\Versioned\Comment;
use AppBundle\Entity\Traits\Versioned\Name;
use AppBundle\Entity\Asset\Asset;

/**
 * EventItem
 *
 * @ORM\Entity()
 * @ORM\Table(name="event_item")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Schedule\EventLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EventItem
{

    use Name,
        Comment,
        TimestampableEntity,
        SoftDeleteableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="items")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Asset\Asset")
     * @ORM\JoinColumn(name="asset_id", referencedColumnName="id")
     */
    private $asset = null;

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
     * Set id
     *
     * @return integer
     */
    public function setId( $id )
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set asset
     *
     * @param string $asset
     *
     * @return EventItem
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
