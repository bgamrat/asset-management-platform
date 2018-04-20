<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Traits\InUse;
use App\Entity\Traits\Comment;
use App\Entity\Traits\Id;
use App\Entity\Traits\Name;
use App\Entity\Traits\XDefault;

/**
 * TransferStatus
 *
 * @ORM\Table(name="transfer_status")
 * @ORM\Entity(repositoryClass="App\Repository\TransferStatusRepository")
 * @UniqueEntity("name")
 * @UniqueEntity("id")
 */
class TransferStatus
{

    use InUse,
        Comment,
        Id,
        Name,
        XDefault,
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
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     * 
     */
    private $in_transit = false;
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     * 
     */
    private $location_destination = false;
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     * 
     */
    private $location_unknown = false;

    public function setInTransit( $in_transit )
    {
        $this->in_transit = $in_transit;
    }

    public function isInTransit()
    {
        return $this->in_transit;
    }

    public function setLocationDestination( $location_destination )
    {
        $this->location_destination = $location_destination;
    }

    public function isLocationDestination()
    {
        return $this->location_destination;
    }

    public function setLocationUnknown( $location_unknown )
    {
        $this->location_unknown = $location_unknown;
    }

    public function isLocationUnknown()
    {
        return $this->location_unknown;
    }

}
