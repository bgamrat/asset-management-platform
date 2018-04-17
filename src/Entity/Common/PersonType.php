<?php

Namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Id;
use App\Entity\Traits\Type;
use App\Entity\Traits\InUse;
use App\Entity\Traits\Comment;

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
