<?php

namespace AppBundle\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\Type;
use AppBundle\Entity\Traits\InUse;
use AppBundle\Entity\Traits\Comment;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonTypeRepository")
 * @ORM\Table(name="person_type")
 */
class PersonType
{
    use Id,
        Type,
        Comment,
        InUse;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

}
