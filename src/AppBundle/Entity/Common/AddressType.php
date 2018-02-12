<?php

namespace AppBundle\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\Type;

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
