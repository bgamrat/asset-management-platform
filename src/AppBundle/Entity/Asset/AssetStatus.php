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
 * @ORM\Table(name="asset_status")
 * @ORM\Entity()
 * @UniqueEntity("name")
 * @UniqueEntity("id")
 */
class AssetStatus
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
     * @ORM\OneToMany(targetEntity="Asset", mappedBy="id")
     */
    private $id;
    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $available = true;

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

    public function setAvailable( $available )
    {
        $this->available = $available;
    }

    public function isAvailable()
    {
        return $this->available;
    }

}
