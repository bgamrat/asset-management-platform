<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\User;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonRepository")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity("fos_user_id")
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
     * @ORM\OneToOne(targetEntity="User", inversedBy="person")
     * @ORM\JoinColumn(name="fos_user_id", referencedColumnName="id")
     */
    private $user = null;
    /**
     * @ORM\ManyToMany(targetEntity="PhoneNumber", cascade={"persist"})
     * @ORM\JoinTable(name="person_phone_number",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="phone_number_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $phoneNumbers = null;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $deletedAt;

    public function __construct()
    {
        $this->phoneNumbers = new ArrayCollection();
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
    public function getUser()
    {
        return $this->user;
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
    
    public function getPhonenumbers() {
        return $this->phoneNumbers;
    }

    public function addPhonenumber( PhoneNumber $phoneNumber )
    {
        if (!$this->phoneNumbers->contains($phoneNumber)) {
            $this->phoneNumbers->add( $phoneNumber );
        }
    }

    public function removePhonenumber( PhoneNumber $phoneNumber )
    {
        $this->phoneNumbers->removeElement( $phoneNumber );
    }

}
