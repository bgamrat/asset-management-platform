<?php

Namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Id;
use App\Entity\Traits\Type;

/**
 * @ORM\Entity
 * @ORM\Table(name="address_type")
 */
class AddressType
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
