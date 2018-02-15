<?php

namespace AppBundle\Entity\Staff;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\Type;
use AppBundle\Entity\Traits\Active;
use AppBundle\Entity\Traits\Comment;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleTypeRepository")
 * @ORM\Table(name="role_type")
 */
class RoleType
{
    use Id,
        Type,
        Comment,
        Active;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

}
