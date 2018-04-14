<?php

Namespace App\Entity\Schedule;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Entity\Traits\InUse;
use Entity\Traits\Comment;
use Entity\Traits\Id;
use Entity\Traits\Name;

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
