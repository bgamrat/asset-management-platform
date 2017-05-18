<?php

namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Asset\Transfer;
use AppBundle\Entity\Common\Person;

/**
 * Carrier
 *
 * @ORM\Table(name="carrier")
 * @ORM\Entity()
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Carrier
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
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true, unique=true)
     * @Gedmo\Versioned
     */
    private $name;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="active", type="boolean") 
     */
    private $active = true;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $comment;
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Common\Person", cascade={"persist"})
     * @ORM\JoinTable(name="carrier_contact",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="carrier_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $contacts = null;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $account_information = null;
    /**
     * @var ArrayCollection $services
     * @ORM\OneToMany(targetEntity="CarrierService", mappedBy="carrier", cascade={"persist"})
     */
    private $services = null;
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
        $this->contacts = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @return integer
     */
    public function setId( $id )
    {
        $this->id = $id;
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
     * @return Carrier
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

    public function setActive( $active )
    {
        $this->active = $active;
    }

    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Carrier
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

    public function getContacts()
    {
        return $this->contacts->toArray();
    }

    public function addContact( Person $contact )
    {
        if( !$this->contacts->contains( $contact ) )
        {
            $this->contacts->add( $contact );
        }
    }

    public function removeContact( Person $contact )
    {
        $this->contacts->removeElement( $contact );
    }

    /**
     * Set AccountInformation
     *
     * @param string $accountInformation
     *
     * @return Carrier
     */
    public function setAccountInformation( $accountInformation )
    {
        $this->account_information = $accountInformation;

        return $this;
    }

    /**
     * Get AccountInformation
     *
     * @return string
     */
    public function getAccountInformation()
    {
        return $this->account_information;
    }

    public function getServices()
    {
        return $this->services->toArray();
    }

    public function addService( CarrierService $service )
    {
        if( !$this->services->contains( $service ) )
        {
            $this->services->add( $service );
            $service->setCarrier($this);
        }
    }

    public function removeService( CarrierService $service )
    {
        $this->services->removeElement( $service );
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
        $this->setActive( false );
    }

}
