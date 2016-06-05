<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AddressRepository")
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
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="AddressType")
     * @ORM\OrderBy({"type" = "ASC"})
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;
    
    /**
     * @var string
     *
     * @ORM\Column(name="street1", type="string", length=64, nullable=true, unique=false)
     */
    private $street1;

    /**
     * @var string
     *
     * @ORM\Column(name="street2", type="string", length=64, nullable=true, unique=false)
     */
    private $street2;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=64, nullable=false, unique=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="state_province", type="string", length=32, nullable=false, unique=false)
     */
    private $state_province;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code", type="string", length=16, nullable=true, unique=false)
     */
    private $postal_code;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=2, nullable=true)
     */
    private $country;
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $comment;
    
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
     * Set street1
     *
     * @param string $street1
     *
     * @return Address
     */
    public function setStreet1($street1)
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
    public function setStreet2($street2)
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
    public function setCity($city)
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
    public function setStateProvince($state_province)
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
    public function setPostalCode($postalCode)
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
    public function setCountry($country)
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
    
    public function toArray()
    {
        return [
            'type' => $this->getType(),
            'street1' => $this->getStreet1(),
            'street2' => $this->getStreet2(),
            'city' => $this->getCity(),
            'state_province' => $this->getStateProvince(),
            'postal_code' => $this->getPostalCode(),
            'country' => $this->getCountry()
        ];
    }
}