<?php

namespace AppBundle\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\User;
use AppBundle\Entity\Common\Email;
use AppBundle\Entity\Common\Phone;
use AppBundle\Entity\Common\Address;
use AppBundle\Entity\Common\PersonLog;
use AppBundle\Entity\Traits\Versioned\Active;
use AppBundle\Entity\Traits\Versioned\Comment;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\History;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonRepository")
 * @Gedmo\Loggable(logEntryClass="PersonLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity("user")
 */
class Person
{

    use Active,
        Comment,
        Id,
        TimestampableEntity,
        SoftDeleteableEntity,
        History;

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
     *      min = 0,
     *      max = 64,
     *      minMessage = "person.firstname error.must_be_at_least {{ limit }} common.characters",
     *      maxMessage = "person.firstname error.must_be_less_than_or_equal_to {{ limit }} common.characters",
     * )
     * @Assert\Expression(expression = "value=='' or this.getTitle() == ''",
     *      message="person.firstname is required if no title is provided"
     * )
     * @ORM\Column(name="firstname", type="string", length=64, nullable=false, unique=false)
     * @Gedmo\Versioned
     */
    private $firstname;
    /**
     * @var string
     *
     * @Assert\Length(
     *      min = 2,
     *      max = 64,
     *      minMessage = "person.middlename error.must_be_at_least {{ limit }} common.characters",
     *      maxMessage = "person.middlename error.must_be_less_than_or_equal_to {{ limit }} common.characters",
     * )
     * @ORM\Column(name="middlename", type="string", length=64, nullable=true, unique=false)
     * @Gedmo\Versioned
     */
    private $middlename;
    /**
     * @var string
     *
     * @Assert\Length(
     *      min = 0,
     *      max = 64,
     *      minMessage = "person.lastname error.must_be_at_least {{ limit }} common.characters",
     *      maxMessage = "person.lastname error.must_be_less_than_or_equal_to {{ limit }} common.characters",
     * )
     * @Assert\Expression(expression = "value=='' or this.getTitle() == ''",
     *      message="person.lastname is required if no title is provided"
     * )
     * @ORM\Column(name="lastname", type="string", length=64, nullable=false, unique=false)
     * @Gedmo\Versioned
     */
    private $lastname;
    /**
     * @var string
     *
     * @Assert\Length(
     *      min = 2,
     *      max = 64,
     *      minMessage = "person.title error.must_be_at_least {{ limit }} common.characters",
     *      maxMessage = "person.title error.must_be_less_than_or_equal_to {{ limit }} common.characters",
     * )
     * @Assert\Expression(expression = "value=='' or this.getLastname() == ''",
     *      message="person.title is required if no name is provided"
     * )
     * @ORM\Column(name="title", type="string", length=64, nullable=true, unique=false)
     * @Gedmo\Versioned
     */
    private $title;
    /**
     * @var ArrayCollection $phones
     * @ORM\ManyToMany(targetEntity="Phone", cascade={"persist"})
     * @ORM\JoinTable(name="person_phone",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="phone_id", referencedColumnName="id", unique=true, nullable=false)}
     *      )
     */
    private $phones;
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
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="person", fetch="EXTRA_LAZY", cascade="remove")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * @Gedmo\Versioned
     */
    private $user = null;
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Staff\PersonRole", cascade={"persist"})
     * @ORM\JoinTable(name="staff_role",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"start" = "DESC"})
     */
    private $roles = null;

    public function __construct()
    {
        $this->phones = new ArrayCollection();
        $this->emails = new ArrayCollection();
        $this->addresses = new ArrayCollection();
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
     * Set middlename
     *
     * @param string $middlename
     *
     * @return Person
     */
    public function setMiddlename( $middlename )
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * Get middlename
     *
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middlename;
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
     * Set title
     *
     * @param string $title
     *
     * @return Person
     */
    public function setTitle( $title )
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
        if( !empty( $this->middlename ) )
        {
            $name[] = $this->middlename;
        }
        if( !empty( $this->lastname ) )
        {
            $name[] = $this->lastname;
        }
        if( count( $name ) === 0 )
        {
            if( !empty( $this->title ) )
            {
                $name[] = $this->title;
            }
        }
        return implode( ' ', $name );
    }

    /**
     * Set fosUserId
     *
     * @param int $user
     *
     * @return Person
     */
    public function setUser( User $user = null )
    {
        $this->user = $user;

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

    public function getPhones()
    {
        return $this->phones->toArray();
    }

    public function addPhone( Phone $phone )
    {
        if( !$this->phones->contains( $phone ) )
        {
            $this->phones->add( $phone );
        }
    }

    public function removePhone( Phone $phone )
    {
        $this->phones->removeElement( $phone );
    }

    public function getPhoneLines()
    {
        $phones = $this->getPhones();
        $phoneLines = [];
        foreach( $phones as $p )
        {
            $phoneLines[] = $p->getType()->getType() . ' ' . $p->getPhone();
        }
        return $phoneLines;
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

    public function getEmailLines()
    {
        $emails = $this->getEmails();
        $emailLines = [];
        foreach( $emails as $e )
        {
            $emailLines[] = $e->getType()->getType() . ' ' . $e->getEmail();
        }
        return $emailLines;
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

    public function getRoles()
    {
        return $this->roles->toArray();
    }

    public function addRole( Role $role )
    {
        if( !$this->roles->contains( $role ) )
        {
            $this->roles->add( $role );
        }
    }

    public function removeRole( Role $role )
    {
        $this->roles->removeElement( $role );
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
