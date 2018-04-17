<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Traits\Versioned\Active;
use App\Entity\Traits\Id;

/**
 * Tractor
 *
 * @ORM\Table(name="tractor")
 * @ORM\Entity()
 * @Gedmo\Loggable()
 * @UniqueEntity("name")
 * @UniqueEntity("id")
 */
class Tractor
{

    use Active,
        Id,
        TimestampableEntity,
        SoftDeleteableEntity;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Transfer", mappedBy="id")
     */
    private $id;

}
