<?php

namespace AppBundle\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\Type;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonTypeRepository")
 * @ORM\Table(name="person_type")
 */
class PersonType
{
    use Id,
        Type;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

}
