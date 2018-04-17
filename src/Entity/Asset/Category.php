<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Traits\Versioned\InUse;
use App\Entity\Traits\Versioned\Comment;
use App\Entity\Traits\Id;
use App\Entity\Traits\Versioned\Name;
use App\Entity\Traits\Versioned\Value;

/**
 * Category
 *
 * @Gedmo\Loggable
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="Repository\CategoryRepository")
 * @UniqueEntity("name")
 * @UniqueEntity("id")
 */
class Category
{

    use InUse,
        Comment,
        Id,
        Name,
        Value;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Model", mappedBy="id")
     */
    private $id;
    /**
     * @var string
     *   
     * @ORM\Column(name="full_name", type="string", nullable=true, unique=true)
     */
    private $fullName;
    /**
     * @var integer
     * 
     * @ORM\Column(type="integer", nullable=false)
     */
    private $position = 0;
    /**
     * @Assert\Expression(expression="this !== this.getParent()", message="No self-referencing please")
     * @ORM\ManyToOne(targetEntity="Category", fetch="EAGER",  cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent = null;

    /**
     * Set fullname
     *
     * @param string $fullName
     *
     * @return string
     */
    public function setFullName()
    {
        $fullName = $this->name;
        $parent = $this->parent;
        while( $parent !== null && $parent->name !== 'top' )
        {
            $fullName = $parent->name . '-' . $fullName;
            $parent = $parent->parent;
        }
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set position
     *
     * @param string $position
     *
     * @return string
     */
    public function setPosition( $position )
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set parent
     *
     * @param string $parent
     *
     * @return Email
     */
    public function setParent( $parent )
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

}
