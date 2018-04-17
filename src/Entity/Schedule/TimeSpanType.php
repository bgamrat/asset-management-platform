<?php

Namespace App\Entity\Schedule;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\Traits\InUse;
use App\Entity\Traits\Comment;
use App\Entity\Traits\Id;
use App\Entity\Traits\Name;

/**
 * Status
 *
 * @ORM\Table(name="time_span_type")
 * @ORM\Entity()
 * @UniqueEntity("name")
 * @UniqueEntity("id")
 */
class TimeSpanType
{

    use InUse,
        Comment,
        Id,
        Name;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="TimeSpan", mappedBy="id")
     */
    private $id;
}
