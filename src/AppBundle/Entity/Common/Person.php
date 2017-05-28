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
 * @UniqueEntity("user")
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
    private $contact_id = null;
    private $contact_name = null;
    private $contact_entity_type = null;
    private $contact_entity_id = null;

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

    public function getPhoneLines()
    {
        $phones = $this->getPhones();
        $phoneLines = [];
        foreach( $phones as $p )
        {
            $phoneLines[] = $p->getType()->getType() . ' ' . $p->getPhoneNumber();
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

    public function setContactId( $contactId )
    {
        $this->contact_id = $contactId;
        return $this;
    }

    public function getContactId()
    {
        return $this->contact_id;
    }
    
    public function setContactEntityType( $contactEntityType )
    {
        $this->contact_entity_type = $contactEntityType;
        return $this;
    }

    public function getContactEntityType()
    {
        return $this->contact_entity_type;
    }

    // Contact Entity Id is the entity id of the organization of the selected contact
    public function setContactEntityId( $contactEntityId )
    {
        $this->contact_entity_id = $contactEntityId;
        return $this;
    }

    public function getContactEntityId()
    {
        return $this->contact_entity_id;
    }

    public function setContactName( $contactName )
    {
        $this->contact_name = $contactName;
        return $this;
    }

    public function getContactName()
    {
        return $this->contact_name;
    }

    function getContactDetails()
    {
        $details = [];

        $d = [];
        
        $d['person_id'] = $this->getId();
        $d['name'] = $this->getContactName();
        $d['contact_id'] = $this->getContactId();
        $d['contact_entity_id'] = $this->getContactEntityId();
        $d['contact_type'] = $this->getContactEntityType();

        $phoneLines = $this->getPhoneLines();
        if( count( $phoneLines ) > 0 )
        {
            $phoneLines = implode( '<br>', $phoneLines ) . '<br>';
        }
        else
        {
            $phoneLines = '';
        }
        $emailLines = $this->getEmailLines();
        if( count( $emailLines ) > 0 )
        {
            $emailLines = implode( '<br>', $emailLines ) . '<br>';
        }
        else
        {
            $emailLines = '';
        }

        // HTML label attributes for dijit.FilteringSelects MUST start with a tag
        $labelBase = '<div>' . $d['name'] . '<br>'
                . $phoneLines
                . $emailLines;
        $d['label'] = $labelBase;
        $addresses = $this->getAddresses();
        $d['hash'] = $this->getContactEntityType().'/'.$this->getContactEntityId().'/'.$this->getId();
        if( !empty( $addresses ) )
        {
            $hashBase = $d['hash'];
            foreach( $addresses as $a )
            {
                $d['address_id'] = $a->getId();
                $d['label'] .= nl2br( $a->getAddress() ) . '</div>';
                $d['label'] = $labelBase;
                $d['hash'] = $hashBase .'/'.$a->getId();
                $details[] = $d;
            }
        }
        else
        {
            $d['label'] .= '</div>';
            $details[] = $d;
        }

        return $details;
    }
}
