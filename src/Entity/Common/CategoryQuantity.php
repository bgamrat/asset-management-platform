<?php

Namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Id;
use App\Entity\Traits\Versioned\Quantity;
use App\Entity\Traits\Versioned\Value;

/**
 * CategoryQuantity
 *
 * @ORM\Table(name="category_quantity")
 * @Gedmo\Loggable
 * @ORM\Entity()
 * 
 */
class CategoryQuantity
{

    use Comment,
        Id,
        Value,
        Quantity,
        TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="\Entity\Asset\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $category;

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Category
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
