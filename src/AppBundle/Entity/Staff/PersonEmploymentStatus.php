<?php

Namespace AppBundle\Entity\Staff;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\DateSpan;

/**
 * PersonEmploymentStatus
 *
 * @ORM\Table(name="person_employment_status")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Common\PersonLog")
 * @ORM\Entity()
 *
 */
class PersonEmploymentStatus
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Common\Person", inversedBy="employment_statuses")
     * @ORM\JoinColumn(name="person", referencedColumnName="id" )
     * @ORM\OrderBy({"type" = "ASC"})
     */
    protected $person;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Staff\EmploymentStatus")
     * @ORM\JoinColumn(name="employment_status_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $employment_status;

    /**
     * Set person
     *
     * @param int $person
     *
     * @return PersonEmploymentStatus
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
     * Set employment_status
     *
     * @param string $employment_status
     *
     * @return PersonEmploymentStatus
     */
    public function setEmploymentStatus( $employment_status )
    {
        $this->employment_status = $employment_status;

        return $this;
    }

    /**
     * Get employment_status
     *
     * @return string
     */
    public function getEmploymentStatus()
    {
        return $this->employment_status;
    }

}
