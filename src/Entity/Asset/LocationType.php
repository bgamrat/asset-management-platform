<?php

Namespace App\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Traits\InUse;
use App\Entity\Traits\Id;
use App\Entity\Traits\Name;
use App\Entity\Traits\XDefault;

/**
 * Location
 *
 * @ORM\Table(name="location_type")
 * @ORM\Entity(repositoryClass="App\Repository\LocationTypeRepository")
 * @UniqueEntity("name")
 * @UniqueEntity("id")
 */
class LocationType
{

    use InUse,
        Id,
        Name,
        XDefault;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Location", mappedBy="id")
     */
    private $id;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $entity;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $location;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $url;

    /**
     * Set entity
     *
     * @param string $entity
     *
     * @return LocationType
     */
    public function setEntity( $entity )
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return LocationType
     */
    public function setLocation( $location )
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return LocationType
     */
    public function setUrl( $url )
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

}
