<?php

namespace AppBundle\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Common\AddressLog")
 * @ORM\Entity()
 */
class Address
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Common\AddressType")
     * @ORM\OrderBy({"type" = "ASC"})
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $type;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="street1", type="string", length=64, nullable=true, unique=false)
     */
    private $street1;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="street2", type="string", length=64, nullable=true, unique=false)
     */
    private $street2;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="city", type="string", length=64, nullable=false, unique=false)
     */
    private $city;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="state_province", type="string", length=32, nullable=true, unique=false)
     * @Assert\Regex("/^(A[BKLRZ]|BC|C[AOT]|D[CE]|FL|GA|HI|I[ADLN]|K[SY]|LA|M[ABDEINOST]|N[BCDEHJLMSTUVY]|O[HKNR]|P[AE]|QC|RI|S[CDK]|T[NX]|UT|V[AT]|W[AIVY|YT]$/")
     */
    private $state_province;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="postal_code", type="string", length=16, nullable=true, unique=false)
     */
    private $postal_code;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="country", type="string", length=2, nullable=true)
     */
    private $country;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $comment;
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
     * @return integer
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
     * @return Address
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
     * Set street1
     *
     * @param string $street1
     *
     * @return Address
     */
    public function setStreet1( $street1 )
    {
        $this->street1 = $street1;

        return $this;
    }

    /**
     * Get street1
     *
     * @return string
     */
    public function getStreet1()
    {
        return $this->street1;
    }

    /**
     * Set street2
     *
     * @param string $street2
     *
     * @return Address
     */
    public function setStreet2( $street2 )
    {
        $this->street2 = $street2;

        return $this;
    }

    /**
     * Get street2
     *
     * @return string
     */
    public function getStreet2()
    {
        return $this->street2;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Address
     */
    public function setCity( $city )
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Address
     */
    public function setStateProvince( $state_province )
    {
        $this->state_province = $state_province;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getStateProvince()
    {
        return $this->state_province;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return Address
     */
    public function setPostalCode( $postalCode )
    {
        $this->postal_code = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Address
     */
    public function setCountry( $country )
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
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

    public function getAddress()
    {
        $addr = [$this->getType()->getType(), $this->street1];
        if( !empty( $this->street2 ) )
        {
            $addr[] = $this->street2;
        }
        $addr[] = $this->city . ', ' . $this->state_province . '  ' . $this->postal_code;
        if( !empty( $this->country ) )
        {
            $addr[] = $this->country;
        }
        return implode( PHP_EOL, $addr );
    }

}
