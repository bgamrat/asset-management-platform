<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Asset\Transfer;
use App\Entity\Common\Person;
use App\Entity\Traits\Active;
use App\Entity\Traits\Comment;
use App\Entity\Traits\Id;
use App\Entity\Traits\Name;

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

    use Active,
        Comment,
        Id,
        Name,
        TimestampableEntity,
        SoftDeleteableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Common\Person", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="carrier_contact",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="carrier_id", referencedColumnName="id")}
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
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $tracking_url;
    /**
     * @var ArrayCollection $services
     * @ORM\OneToMany(targetEntity="CarrierService", mappedBy="carrier", cascade={"persist"})
     */
    private $services = null;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->services = new ArrayCollection();
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

    /**
     * Set TrackingUrl
     *
     * @param string $trackingUrl
     *
     * @return Carrier
     */
    public function setTrackingUrl( $trackingUrl )
    {
        $this->tracking_url = $trackingUrl;

        return $this;
    }

    /**
     * Get TrackingUrl
     *
     * @return string
     */
    public function getTrackingUrl()
    {
        return $this->tracking_url;
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
            $service->setCarrier( $this );
        }
    }

    public function removeService( CarrierService $service )
    {
        $this->services->removeElement( $service );
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
