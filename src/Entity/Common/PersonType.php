<?php

Namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Entity\Traits\Id;
use Entity\Traits\Type;
use Entity\Traits\InUse;
use Entity\Traits\Comment;

/**
 * @ORM\Entity(repositoryClass="Repository\PersonTypeRepository")
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
