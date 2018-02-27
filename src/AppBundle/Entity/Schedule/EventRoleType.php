<?php

namespace AppBundle\Entity\Schedule;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Traits\InUse;
use AppBundle\Entity\Traits\Comment;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\Name;

/**
 * Status
 *
 * @ORM\Table(name="event_role_type")
 * @ORM\Entity()
 * @UniqueEntity("name")
 * @UniqueEntity("id")
 */
class EventRoleType
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
     * @ORM\OneToMany(targetEntity="EventRole", mappedBy="id")
     */
    private $id;
}
