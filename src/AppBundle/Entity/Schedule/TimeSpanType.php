<?php

namespace AppBundle\Entity\Schedule;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Traits\Active;
use AppBundle\Entity\Traits\Comment;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\Name;

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

    use Active,
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
