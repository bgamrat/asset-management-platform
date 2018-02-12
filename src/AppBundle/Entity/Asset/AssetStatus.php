<?php

namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Traits\Active;
use AppBundle\Entity\Traits\Available;
use AppBundle\Entity\Traits\Comment;
use AppBundle\Entity\Traits\Id;
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

    use Id,
        Active,
        Available,
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
}
