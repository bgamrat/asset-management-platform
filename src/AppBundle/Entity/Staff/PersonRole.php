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
 * PersonRole
 *
 * @ORM\Table(name="person_role")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Common\PersonLog")
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
     * @ORM\OneToOne(targetEntity="\AppBundle\Entity\Staff\Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $role;

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
