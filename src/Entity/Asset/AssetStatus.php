<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Entity\Traits\InUse;
use Entity\Traits\Available;
use Entity\Traits\Comment;
use Entity\Traits\Id;
use Entity\Traits\Name;
use Entity\Traits\XDefault;

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
        InUse,
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
