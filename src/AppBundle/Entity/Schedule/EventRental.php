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
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\Versioned\Name;
use AppBundle\Entity\Traits\Versioned\Cost;
use AppBundle\Entity\Asset\Asset;

/**
 * EventRental
 *
 * @ORM\Entity()
 * @ORM\Table(name="event_rental")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Schedule\EventLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EventRental
{

    use Id,
        Comment,
        Cost,
        Name,
        SoftDeleteableEntity,
        TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="rentals")
     * @ORM\JoinColumn(name="rental_id", referencedColumnName="id")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Asset\Asset")
     * @ORM\JoinColumn(name="asset_id", referencedColumnName="id")
     */
    private $asset = null;

    /**
     * Set asset
     *
     * @param string $asset
     *
     * @return EventRental
     */
    public function setAsset( $asset = null )
    {
        if( !empty( $asset ) )
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
        }
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
