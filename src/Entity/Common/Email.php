<?php

Namespace App\Entity\Common;

use App\Entity\Common\EmailType;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\Traits\Comment;
use App\Entity\Traits\Id;

/**
 * @ORM\Entity
 * @ORM\Table(name="email")
 * @UniqueEntity("email")
 * @Gedmo\Loggable
 */
class Email
{

    use Comment,
        Id;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="EmailType")
     * @ORM\OrderBy({"type" = "ASC"})
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;
    /**
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[0-9x\.\,\ \+\(\)-]{2,24}$/", message="error.invalid_email")
     * @ORM\Column(type="string", length=24, name="email", nullable=false)
     */
    private $email;

    /**
     * Set type
     *
     * @param int $type
     *
     * @return Email
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
     * Set email
     *
     * @param string $email
     *
     * @return Email
     */
    public function setEmail( $email )
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

}
