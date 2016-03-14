<?php

// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class User extends BaseUser
{

    const ROLE_API = 'ROLE_API';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Assert\NotBlank(message="fos_user.email_blank")
     * @Assert\Email(message="fos_user.email.invalid")
     * @Gedmo\Versioned
     * @var string
     */
    protected $email;
    /**
     * @Assert\NotBlank(message="fos_user.username.blank")
     * @Assert\Length(min=2,max=255,minMessage="fos_user.username.short",maxMessage="fos_user.username.long")
     * @Gedmo\Versioned
     * @var string
     */
    protected $username;
    /**
     * @ Assert\NotBlank(message="fos_user.password_blank")
     * @ Assert\Length(min=8,max=4096,minMessage="error.password.short",maxMessage="error.password.long")
     * @var string
     */
    protected $password;
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $deletedAt;
    /**
     * @Assert\Choice(multiple=true, min=0, choices = {"ROLE_USER","ROLE_ADMIN","ROLE_SUPER_ADMIN","ROLE_API"}, message = "Invalid role")
     * @Gedmo\Versioned
    */
    protected $roles;
    /**
     * @ORM\OneToOne(targetEntity="Invitation")
     * @ORM\JoinColumn(referencedColumnName="code")
     * @Assert\NotNull(message="Your invitation is wrong", groups={"Registration"})
     */
    protected $invitation;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /*
     * This supports the form validation code
     */
    public function addRole($role) {
        if (is_object($role)) {
            if (isset($role->name)) {
                $role = $role->name;
            }
        }
        parent::addRole($role);
    }
    
    public function setInvitation( Invitation $invitation )
    {
        $this->invitation = $invitation;
    }

    public function getInvitation()
    {
        return $this->invitation;
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
