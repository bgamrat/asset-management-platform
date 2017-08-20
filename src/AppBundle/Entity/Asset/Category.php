<?php

namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Category
 *
 * @Gedmo\Loggable
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 * @UniqueEntity("name")
 * @UniqueEntity("id")
 */
class Category
{

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
     * @ORM\Column(name="name", type="string", length=64, nullable=false, unique=false)
     * @Assert\NotBlank(
     *     message = "blank.name")
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9x\.\,\ \+\(\)-]{2,32}$/",
     *     htmlPattern = "^[a-zA-Z0-9x\.\,\ \+\(\)-]{2,32}$",
     *     message = "invalid.name {{ value }}",
     *     match=true)
     */
    private $name;
    /**
     * @var string
     *   
     * @ORM\Column(name="full_name", type="string", nullable=true, unique=true)
     */
    private $fullName;
    /**
     * @var float

     * @ORM\Column(name="value", type="float", nullable=true, unique=false)
     */
    private $value = 0.0;
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
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $comment;
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     * 
     */
    private $active = true;

    /**
     * Set id
     *
     * @return integer
     */
    public function setId( $id )
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName( $name )
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

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
     * Set value
     *
     * @param float $value
     *
     * @return Category
     */
    public function setValue( $value )
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
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

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Email
     */
    public function setComment( $comment )
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    public function setActive( $active )
    {
        $this->active = $active;
        return $this;
    }

    public function isActive()
    {
        return $this->active;
    }

}
