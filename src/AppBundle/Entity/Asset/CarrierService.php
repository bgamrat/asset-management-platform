<?php

namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Traits\Active;
use AppBundle\Entity\Traits\Comment;
use AppBundle\Entity\Traits\Name;
use AppBundle\Entity\Traits\XDefault;

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
     * Set id
     *
     * @return integer
     */
    public function setId( $id )
    {
        $this->id = $id;
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
