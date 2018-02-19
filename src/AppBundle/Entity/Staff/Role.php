<?php

namespace AppBundle\Entity\Staff;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Traits\Id;
use AppBundle\Entity\Traits\Versioned\Name;
use AppBundle\Entity\Traits\Versioned\Active;
use AppBundle\Entity\Traits\Versioned\Comment;
use AppBundle\Entity\Traits\Versioned\XDefault;
use AppBundle\Entity\Traits\History;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 * @ORM\Table(name="role")
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Staff\RoleLog")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Role
{

    use Id,
        Name,
        Comment,
        Active,
        XDefault,
        TimestampableEntity,
        SoftDeleteableEntity,
        History;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var ArrayCollection $roles
     * @Assert\All({
     * @Assert\Regex(
     *     pattern="/^ROLE_([A-Z]+_?)+$/",
     *     message = "invalid.role {{ value }}",
     *     match=true)
     * })
     * @Gedmo\Versioned
     */
    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getRoles()
    {
        return $this->roles->toArray();
    }

    public function addRole( string $role )
    {
        if( !$this->roles->contains( $role ) )
        {
            $this->roles->add( $role );
        }
    }

    public function removeRole( string $role )
    {
        $this->roles->removeElement( $role );
    }

    public function setDeletedAt( $deletedAt )
    {
        $this->deletedAt = $deletedAt;
        $this->setActive( false );
    }

}
