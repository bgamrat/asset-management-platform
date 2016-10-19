<?php

namespace AppBundle\Entity\Common;

use AppBundle\Entity\Common\PhoneNumberType;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="phone_number")
 * @Gedmo\Loggable
 */
class PhoneNumber
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="PhoneNumberType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * @ORM\OrderBy({"type" = "ASC"})
     */
    private $type;
    /**
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[0-9x\.\,\ \+\(\)-]{2,24}$/", message="error.invalid_phone_number")
     * @ORM\Column(type="string", length=24, name="phone_number", nullable=false)
     */
    private $phone_number;
    /**
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private $comment;
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return PhoneNumber
     */
    public function setType( $type )
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     *
     * @return PhoneNumber
     */
    public function setPhoneNumber( $phoneNumber )
    {
        $this->phone_number = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return PhoneNumber
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
    
    public function toArray()
    {
        return [
            'type' => $this->getType(),
            'phone_number' => $this->getPhoneNumber(),
            'comment' => $this->getComment()
        ];
    }

}
