<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

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
     *
     * @ORM\Column(name="firstname", type="string", length=64, nullable=true, unique=true)
     * @Gedmo\Versioned
     */
    private $firstname;
    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=64, nullable=true, unique=true)
     * @Gedmo\Versioned
     */
    private $lastname;
    /**
     * @var string
     *
     * @ORM\Column(name="middleinitial", type="string", length=1, nullable=true, unique=true)
     * @Gedmo\Versioned
     */
    private $middleinitial;
    /**
     * @var int
     *
     * @ORM\Column(name="fos_user_id", type="integer", nullable=true, unique=true)
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $fos_user_id;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $deletedAt;

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
     * @param int $fosUserId
     *
     * @return Person
     */
    public function setFosUserId( $fosUserId )
    {
        $this->fos_user_id = $fosUserId;

        return $this;
    }

    /**
     * Get fosUserId
     *
     * @return int
     */
    public function getFosUserId()
    {
        return $this->fos_user_id;
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

}
