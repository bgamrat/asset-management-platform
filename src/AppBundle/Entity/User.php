<?php

// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
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
     * @var string
     */
    protected $email;
    
    /**
     * @Assert\NotBlank(message="fos_user.username.blank")
     * @Assert\Length(min=2,max=255,minMessage="fos_user.username.short",maxMessage="fos_user.username.long")
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

    public function setInvitation( Invitation $invitation )
    {
        $this->invitation = $invitation;
    }

    public function getInvitation()
    {
        return $this->invitation;
    }

}