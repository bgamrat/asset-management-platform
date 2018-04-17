<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\Traits\Active;
use App\Entity\Traits\Comment;
use App\Entity\Traits\Id;
use App\Entity\Traits\Name;
use App\Entity\Traits\XDefault;

/**
 * Status
 *
 * @ORM\Table(name="carrier_service")
 * @ORM\Entity()
 * @UniqueEntity("name")
 * @UniqueEntity("id")
 */
class CarrierService
{

    use Active,
        Comment,
            Id,
        Name,
        XDefault;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Carrier", inversedBy="services", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $carrier;

    /**
     * Set carrier
     *
     * @param int $carrier
     *
     * @return CarrierService
     */
    public function setCarrier( $carrier )
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * Get carrier
     *
     * @return int
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

}
