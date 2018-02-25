<?php

namespace AppBundle\Entity\Asset;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Traits\Versioned\InUse;
use AppBundle\Entity\Traits\Versioned\Comment;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\Versioned\XDefault;

/**
 * Status
 *
 * @ORM\Table(name="issue_status")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IssueStatusRepository")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Asset\IssueLog")
 * @UniqueEntity("status")
 * @UniqueEntity("id")
 */
class IssueStatus
{

    use InUse,
        Comment,
        Id,
        XDefault;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="id")
     */
    private $id;
    /**
     * @var integer
     * 
     * @ORM\Column(name="`order`", type="integer", nullable=false)
     */
    private $order = 0;
    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=64, nullable=true, unique=true)
     * @Assert\NotBlank(
     *     message = "blank.name")
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9x\.\,\ \+\(\)-]{2,32}$/",
     *     htmlPattern = "^[a-zA-Z0-9x\.\,\ \+\(\)-]{2,32}$",
     *     message = "invalid.name {{ value }}",
     *     match=true)
     */
    private $status;
    /**
     * @ORM\ManyToMany(targetEntity="IssueStatus")
     * @ORM\JoinTable(name="issue_status_next",
     *      joinColumns={@ORM\JoinColumn(name="next_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id", referencedColumnName="id")}
     *      )
     */
    private $next;

    public function __construct()
    {
        $this->next = new ArrayCollection();
    }

    /**
     * Set order
     *
     * @param string $order
     *
     * @return Order
     */
    public function setOrder( $order )
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Status
     */
    public function setStatus( $status )
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getName()
    {
        return $this->status;
    }

    public function setNext( $statuses )
    {
        foreach( $statuses as $s )
        {
            $this->addNext( $s );
        }
        return $this;
    }

    public function getNext( $full = true )
    {
        $next = [];
        if( count( $this->next ) > 0 )
        {
            if( $full === false )
            {
                foreach( $this->next as $n )
                {
                    $next[] = ['id' => $n->getId(),
                        'status' => $n->getStatus()];
                }
            }
            else
            {
                $next = $this->next;
            }
        }
        return $next;
    }

    public function addNext( IssueStatus $status )
    {
        if( !$this->next->contains( $status ) )
        {
            $this->next->add( $status );
        }
    }

    public function removeNext( IssueStatus $status )
    {
        $this->next->removeElement( $status );
    }

}
