<?php

namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Traits\Versioned\Active;
use AppBundle\Entity\Traits\Versioned\Comment;
use AppBundle\Entity\Traits\Versioned\XDefault;

/**
 * Status
 *
 * @ORM\Table(name="issue_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IssueTypeRepository")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Asset\IssueLog")
 * @UniqueEntity("type")
 * @UniqueEntity("id")
 */
class IssueType
{

    use Comment,
        XDefault,
        Active;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="id")
     */
    private $id;
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=64, nullable=true, unique=false)
     * @Assert\NotBlank(
     *     message = "blank.name")
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9x\.\,\ \+\(\)-]{2,32}$/",
     *     htmlPattern = "^[a-zA-Z0-9x\.\,\ \+\(\)-]{2,32}$",
     *     message = "invalid.type {{ value }}",
     *     match=true)
     */
    private $type;

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
     * Set type
     *
     * @param string $type
     *
     * @return Status
     */
    public function setType( $type )
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

}
