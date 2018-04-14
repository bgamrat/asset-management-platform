<?php

Namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Entity\Traits\Id;
use Entity\Traits\Type;

/**
 * @ORM\Entity
 * @ORM\Table(name="email_type")
 */
class EmailType
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
