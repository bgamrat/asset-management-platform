<?php

namespace AppBundle\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\User;
use AppBundle\Entity\Common\Email;
use AppBundle\Entity\Common\PhoneNumber;
use AppBundle\Entity\Common\Address;
use AppBundle\Entity\Common\PersonLog;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonRepository")
 * @Gedmo\Loggable(logEntryClass="PersonLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Person
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="PersonType")
     * @ORM\OrderBy({"type" = "ASC"})
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $type;
    /**
     * @var string
     * @Assert\Length(
     *      min = 2,
     *      max = 64,
     *      minMessage = "person.firstname error.must_be_at_least {{ limit }} common.characters",
     *      maxMessage = "person.firstname error.must_be_less_than_or_equal_to {{ limit }} common.characters",
     * )
     * @ORM\Column(name="firstname", type="string", length=64, nullable=false, unique=false)
     * @Gedmo\Versioned
     */
    private $firstname;
    /**
     * @var string
     *
     * @Assert\Length(
     *      min = 1,
     *      max = 1,
     *      exactMessage = "person.middleinitial error.must_be_exactly {{ limit }} common.character"
     * )
     * @ORM\Column(name="middleinitial", type="string", length=1, nullable=true, unique=false)
     * @Gedmo\Versioned
     */
    private $middleinitial;
    /**
     * @var string
     *
     * @Assert\Length(
     *      min = 2,
     *      max = 64,
     *      minMessage = "person.lastname error.must_be_at_least {{ limit }} common.characters",
     *      maxMessage = "person.lastname error.must_be_less_than_or_equal_to {{ limit }} common.characters",
     * )
     * @ORM\Column(name="lastname", type="string", length=64, nullable=false, unique=false)
     * @Gedmo\Versioned
     */
    private $lastname;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Gedmo\Versioned
     */
    private $comment;
    /**
     * @var ArrayCollection $phoneNumbers
     * @ORM\ManyToMany(targetEntity="PhoneNumber", cascade={"persist"})
     * @ORM\JoinTable(name="person_phone_number",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="phone_number_id", referencedColumnName="id", unique=true, nullable=false)}
     *      )
     */
    private $phoneNumbers;
    /**
     * @var ArrayCollection $emails
     * @ORM\ManyToMany(targetEntity="Email", cascade={"persist"})
     * @ORM\JoinTable(name="person_email",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="email_id", referencedColumnName="id", unique=true, nullable=false)}
     *      )
     */
    protected $emails;
    /**
     * @var ArrayCollection $addresses
     * @ORM\ManyToMany(targetEntity="Address", cascade={"persist"})
     * @ORM\JoinTable(name="person_address",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="address_id", referencedColumnName="id", unique=true, nullable=false)}
     *      )
     */
    private $addresses;
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     * @Gedmo\Versioned
     */
    private $active = true;
    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="person", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true, nullable=true)
     * @Gedmo\Versioned
     */
    private $user = null;
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $deletedAt;

    public function __construct()
    {
        $this->phoneNumbers = new ArrayCollection();
        $this->emails = new ArrayCollection();
        $this->addresses = new ArrayCollection();
    }

    /**
     * Set id
     * 
     */
    public function setId( $id )
    {
        $this->id = $id;
    }

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
     * @return Person
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
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Person
     */
    public function setFirstname( $firstname )
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Person
     */
    public function setLastname( $lastname )
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set middleinitial
     *
     * @param string $middleinitial
     *
     * @return Person
     */
    public function setMiddleinitial( $middleinitial )
    {
        $this->middleinitial = $middleinitial;

        return $this;
    }

    /**
     * Get middleinitial
     *
     * @return string
     */
    public function getMiddleinitial()
    {
        return $this->middleinitial;
    }

    public function getName()
    {
        return $this->getFullName();
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullName()
    {
        $name = [];
        if( !empty( $this->firstname ) )
        {
            $name[] = $this->firstname;
        }
        if( !empty( $this->middleinitial ) )
        {
            $name[] = $this->middleinitial;
        }
        if( !empty( $this->lastname ) )
        {
            $name[] = $this->lastname;
        }
        return implode( ' ', $name );
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

    /**
     * Set fosUserId
     *
     * @param int $user
     *
     * @return Person
     */
    public function setUser( User $user )
    {
        $this->user = $user;
        if( $user->getPerson() === null )
        {
            $user->setPerson( $this );
        }
        return $this;
    }

    /**
     * Get fosUserId
     *
     * @return int
     */
    public function getUser( $deliver = false )
    {
        return ($deliver === false) ? null : $this->user;
    }

    public function setActive( $active )
    {
        $this->active = $active;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function getPhones()
    {
        return $this->phoneNumbers->toArray();
    }

    public function addPhone( PhoneNumber $phoneNumber )
    {
        if( !$this->phoneNumbers->contains( $phoneNumber ) )
        {
            $this->phoneNumbers->add( $phoneNumber );
        }
    }

    public function removePhone( PhoneNumber $phoneNumber )
    {
        $this->phoneNumbers->removeElement( $phoneNumber );
    }

    public function getEmails()
    {
        return $this->emails->toArray();
    }

    public function addEmail( Email $email )
    {
        if( !$this->emails->contains( $email ) )
        {
            $this->emails->add( $email );
        }
    }

    public function removeEmail( Email $email )
    {
        $this->emails->removeElement( $email );
    }

    public function getAddresses()
    {
        return $this->addresses->toArray();
    }

    public function addAddress( Address $address )
    {
        if( !$this->addresses->contains( $address ) )
        {
            $this->addresses->add( $address );
        }
    }

    public function removeAddress( Address $address )
    {
        $this->addresses->removeElement( $address );
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        if( $this->user !== null )
        {
            $this->user->setEnabled( false );
            $this->user->setLocked( true );
        }
    }
}
