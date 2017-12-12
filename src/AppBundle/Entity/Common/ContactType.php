<?php

namespace AppBundle\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Traits\Active;

/**
 * Contact Type
 *
 * @ORM\Table(name="contact_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContactTypeRepository")
 * @UniqueEntity("name")
 * @UniqueEntity("id")
 */
class ContactType
{

    use Active;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="id")
     */
    private $id;
    /**
     * @var string
     *
     * @Assert\Choice({"carrier", "client", "manufacturer", "other", "vendor", "venue"})
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $entity;

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
     * Set entity
     *
     * @param string $entity
     *
     * @return Email
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

}
