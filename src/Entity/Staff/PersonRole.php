<?php

Namespace App\Entity\Staff;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Entity\Traits\Id;
use Entity\Traits\DateSpan;

/**
 * PersonRole
 *
 * @ORM\Table(name="person_role")
 * @Gedmo\Loggable(logEntryClass="Entity\Common\PersonLog")
 * @ORM\Entity()
 *
 */
class PersonRole
{

    use Id,
        DateSpan,
        TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")

     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Entity\Common\Person", inversedBy="roles")
     * @ORM\JoinColumn(name="person", referencedColumnName="id" )
     * @ORM\OrderBy({"type" = "ASC"})
     */
    protected $person;
    /**
     * @ORM\ManyToOne(targetEntity="Entity\Staff\Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $role;

    /**
     * Set person
     *
     * @param int $person
     *
     * @return PersonRole
     */
    public function setPerson( $person )
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return PersonRole
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
