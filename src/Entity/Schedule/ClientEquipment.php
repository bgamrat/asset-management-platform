<?php

Namespace App\Entity\Schedule;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Id;
use App\Entity\Traits\Versioned\Quantity;

/**
 * ClientEquipment
 *
 * @ORM\Entity()
 * @ORM\Table(name="client_equipment")
 * @Gedmo\Loggable(logEntryClass="App\Entity\Schedule\EventLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ClientEquipment
{

    use Id,
        Comment,
        Quantity,
        SoftDeleteableEntity,
        TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="client_equipment")
     * @ORM\JoinColumn(name="client_equipment_id", referencedColumnName="id")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Asset\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $category;

    /**
     * Set category
     *
     * @param string $category
     *
     * @return ClientEquipment
     */
    public function setCategory( $category )
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

}
