<?php

namespace AppBundle\Entity;

use AppBundle\Entity\ContactType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="contact") 
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Contact
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")

     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="ContactType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;
    
    /**
     * @Assert\NotBlank(message="fos_user.email_blank")
     * @Assert\Email(message="fos_user.email.invalid")
     * @Gedmo\Versioned
     * @var string
     */
    protected $email;

    /**
     * @ORM\OneToOne(targetEntity="Person", mappedBy="contact", cascade={"persist"})
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    protected $person = null;

    /**
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private $comment;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $deletedAt;
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
     * @return Contact
     */
    public function setType($type)
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
     * @return Contact
     */
    public function setPhonenumber($phoneNumber)
    {
        $this->contact = $phoneNumber;
    
        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhonenumber()
    {
        return $this->contact;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Contact
     */
    public function setComment($comment)
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
    
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setEnabled( false );
        $this->setLocked( true );
    }
}
