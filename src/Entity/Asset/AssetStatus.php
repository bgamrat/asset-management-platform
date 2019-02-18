<?php

Namespace App\Entity\Asset;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\Traits\InUse;
use App\Entity\Traits\Available;
use App\Entity\Traits\Comment;
use App\Entity\Traits\Id;
use App\Entity\Traits\Name;
use App\Entity\Traits\XDefault;

/**
 * Status
 *
 * @ORM\Table(name="asset_status")
 * @ORM\Entity()
 * @UniqueEntity("name")
 * @ORM\Cache()
 * @ApiResource
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
     * @Groups({"read"})
     */
    private $id;
}
