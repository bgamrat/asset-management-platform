<?php

Namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Traits\InUse;
use App\Entity\Traits\Id;

/**
 * Contact Type
 *
 * @ORM\Table(name="contact_type")
 * @ORM\Entity(repositoryClass="Repository\ContactTypeRepository")
 * @UniqueEntity("name")
 * @UniqueEntity("id")
 */
class ContactType
{

    use Id,
        InUse;

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
