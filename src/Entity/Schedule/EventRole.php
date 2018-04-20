<?php

Namespace App\Entity\Schedule;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Traits\Comment;
use App\Entity\Traits\Id;
use App\Entity\Traits\DateSpan;

/**
 * EventRole
 *
 * @ORM\Table(name="event_role")
 * @ORM\Entity()
 * @UniqueEntity("id")
 */
class EventRole
{

    use Comment,
        DateSpan,
        Id
    ;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Event", mappedBy="id")
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Common\Person", mappedBy="user", cascade={"persist"})
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", unique=true, nullable=true)
     */
    protected $person = null;
    /**
     * @ORM\ManyToOne(targetEntity="EventRoleType")
     * @ORM\JoinColumn(name="role", referencedColumnName="id")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $role;

    public function setPerson( Person $person = null )
    {
        dump($person);die;
        $this->person = $person;

        return $this;
    }

    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return Event
     */
    public function setRole( $role )
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

}
